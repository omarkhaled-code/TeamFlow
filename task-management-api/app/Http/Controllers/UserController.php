<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json(['msg' => 'user created successfully', 'user' => $user, 'token' => $user->createToken('auth_token')->plainTextToken], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid Data!'],
            ]);
        }
        return response()->json([
            'message' => 'Successfully Login!',
            'user' => $user,
            'token' => $user->createToken("auth_token")->plainTextToken
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('teams');
        return response()->json(['user' => $user], 201);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // التحقق من البيانات
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // تحديث الاسم والإيميل فقط إذا تم إرسالهم
        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        // تحديث الصورة الرمزية إذا تم إرسالها
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إذا موجودة
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }

            // حفظ الصورة الجديدة
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // تعيين صورة افتراضية إذا لم يكن لدى المستخدم صورة
        if (!$user->avatar) {
            $user->avatar = 'avatars/default.jpg'; // ضع هنا المسار للصورة الافتراضية
        }

        $user->save();

        return response()->json([
            'message' => 'user data updated successfully!',
            'user' => $user
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout Successfully']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {

            // 1️⃣ الفرق اللي المستخدم Admin فيها
            $adminTeams = $user->teams()
                ->wherePivot('role', 'admin')
                ->get();

            foreach ($adminTeams as $team) {

                $members = $team->users()->get();

                // 🟥 لو هو العضو الوحيد → احذف الفريق
                if ($members->count() === 1) {
                    $team->delete();
                    continue;
                }

                // 🟩 لو فيه أعضاء → حوّل Admin
                $newAdmin = $members->where('id', '!=', $user->id)->first();

                if ($newAdmin) {
                    $team->users()->updateExistingPivot(
                        $newAdmin->id,
                        ['role' => 'admin']
                    );
                }
            }

            // 2️⃣ حذف join requests
            $user->joinRequests()->delete();

            // 3️⃣ حذف tasks
            $user->tasks()->delete();

            // 4️⃣ الخروج من كل الفرق
            $user->teams()->detach();

            // 5️⃣ حذف tokens
            $user->tokens()->delete();

            // 6️⃣ حذف الحساب
            $user->delete();
        });

        return response()->json([
            'message' => 'Account deleted successfully'
        ], 200);
    }


    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'],
                    'task' => $notification->data['task'] ?? null,
                    'message' => $notification->data['message'] ?? '',
                    'read_at' => $notification['read_at'] ?? null,
                    // REAL time, dynamic
                    'created_at' => $notification->created_at->toDateTimeString(),
                    'created_at_relative' => $notification->created_at->diffForHumans(),
                ];
            });

        if (!$notifications) {
            return response()->json([
                'message' => "ther is no notificatoins for this user!",
            ]);
        }

        return response()->json([
            'notifications' => $notifications
        ], 200);
    }

    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if (! $notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read'
        ], 200);
    }


    public function getUnReadNotificationsCount(Request $request)
    {
        $user = $request->user();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $unreadCount
        ], 200);
    }
    public function getUnReadNotifications(Request $request)
    {
        $user = $request->user();
        $unreadNotifications = $user->unreadNotifications()->count()->get();

        return response()->json([
            'unread_notifications' => $unreadNotifications
        ], 200);
    }

    public function MarkAllAsRead(Request $request)
    {

        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return response()->json([
            'message' => 'All Notification are marked as read'
        ], 200);
    }

    //  public function numbersData(Team $team, Request $request)
    // {
    //     $this->authorize('numbersData', $team);

    //     $user = $request->user();

    //     if($team->isAdmin($user->id)) {            
    //         $data['join_requests_count'] = $team->joinRequests()->count();
    //     }
    //     $data['tasks_count'] = $team->tasks()->count();
    //     $data['member_count'] = $team->users()->count();


    //     return response()->json([
    //         'data' => $data
    //     ]);
    // }

    public function userNumbersData(Request $request)
    {
        $user = $request->user();
        $teams = $user->teams()->get();

        $data['total_teams_count'] = $user->teams()->count();

        foreach ($teams as $team) {
            $data['active_tasks_count'] = $team->tasks()->count();
            $data['join_requests_count'] = $team->joinRequests()->count();
        }

        return response()->json([
            'data' => $data
        ]);
    }


   
}
