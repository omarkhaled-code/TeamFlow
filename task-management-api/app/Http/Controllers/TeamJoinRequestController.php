<?php

namespace App\Http\Controllers;


// use App\Events\TestNotification;

use App\Events\JoinRequestCreated;
use App\Events\MemberEvents;
use App\Events\TeamEvents;
use App\Events\TestNotification;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use JoinRequestCreated as GlobalJoinRequestCreated;

class TeamJoinRequestController extends Controller
{
    use AuthorizesRequests;
    // POST /teams/{team}/add-user
    public function addNewUser(Request $request, Team $team)
    {
        $this->authorize('addUser', $team); // Policy يتحقق إن المستخدم admin

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // تحقق إن المستخدم مش عضو بالفعل
        if ($team->users()->where('user_id', $validated['user_id'])->exists()) {
            return response()->json([
                'message' => 'User is already a member of the team'
            ], 409);
        }

        // ضيف المستخدم
        $team->users()->attach($validated['user_id'], ['role' => 'member']);

        return response()->json([
            'message' => 'User added to the team successfully'
        ], 200);
    }

    // POST /teams/{team}/join-request
    public function storeRequest(Request $request)
    {

        $validated = $request->validate([
            'team_id' => 'required'
        ]);
        $user = $request->user();
        $team = Team::where("id", $validated['team_id'])->first();

        // Cehck if user is not in the team
        $isMember = $team->users()
            ->where('user_id', $user->id)
            ->exists();

        if ($isMember) {
            return response()->json([
                'message' => "you are already member in this team!"
            ]);
        }
        $allowed = $user->joinedTeams()->count() < $user->plan->max_joined_teams;
        if (!$allowed) {
            return response()->json([
                'message' => 'You have reached your team joining limit. Please upgrade your plan.',
                'limit_reached' => true,
            ], 403);
        }


        // تحقق إن مفيش request pending بالفعل
        $existingRequest = TeamJoinRequest::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$existingRequest) {
            // إنشاء طلب جديد
            $joinRequest = TeamJoinRequest::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'status' => 'pending'
            ]);

            if ($joinRequest) {
                // $team->admins->notify(new \App\Notifications\JoinTeamNotifications($joinRequest));
                Notification::send(
                    $team->admins,
                    new \App\Notifications\JoinTeamNotifications($joinRequest)
                );
                // event(new JoinRequestCreated($joinRequest));
                event(new JoinRequestCreated($joinRequest));
                // event(new App\Events\JoinRequestCreated('hello'));


            }
        }



        return response()->json([
            'message' => 'Join request submitted successfully.',
        ], 201);
    }

    // GET /my-join-requests
    public function myJoinRequests(Request $request)
    {

        $user = $request->user();
        $joinRequests = TeamJoinRequest::where('user_id', $user->id)
            ->with('team')
            ->get();

        if ($joinRequests->isEmpty()) {
            return response()->json([
                'message' => 'No join requests found for this user.'
            ], 404);
        }

        return response()->json([
            'join_requests' => $joinRequests
        ], 200);
    }

    // GET /teams/{team}/join-requests (admin only)
    public function teamJoinRequests(Team $team)
    {
        $this->authorize('view', $team); // Policy يتحقق إن المستخدم admin

        $joinRequests = $team->joinRequests()
            ->where('status', 'pending')
            ->with('user')
            ->get();

        return response()->json([
            'join_requests' => $joinRequests
        ], 200);
    }

    // POST /teams/join-requests/{joinRequest}/approve
    public function approve(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required'
        ]);

        $joinRequest = TeamJoinRequest::find($validated['request_id'])->first();

        $this->authorize('approve', $joinRequest);

        // إضافة المستخدم للفريق
        $joinRequest->team->users()->attach($joinRequest->user_id, ['role' => 'member']);

        // أرسل إشعار لكل أعضاء الفريق ما عدا اللي انضموا
        foreach ($joinRequest->team->users as $member) {
            if ($member->id !== $joinRequest->user_id) {
                $member->notify(new \App\Notifications\UserJoinedTeam($joinRequest->user, $joinRequest->team));
            }
        }

        $joinRequest->user->notify(new \App\Notifications\AcceptJoinRequest($joinRequest));
        event(new TeamEvents());
        event(new MemberEvents());

        $joinRequest->delete();

        return response()->json([
            'message' => 'User approved successfully'
        ]);
    }

    // POST /teams/join-requests/{joinRequest}/reject
    public function reject(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required'
        ]);

        $joinRequest = TeamJoinRequest::find($validated['request_id']);
        $this->authorize('reject', $joinRequest);


        $joinRequest->delete();
        // return $joinRequest->user();
        $joinRequest->user->notify(new \App\Notifications\RejectJoinRequest($joinRequest));


        return response()->json([
            'message' => 'User rejected successfully'
        ]);
    }
}
