<?php

namespace App\Http\Controllers;

use App\Events\TeamEvents;
use App\Models\Chat;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TeamController extends Controller
{
    use AuthorizesRequests;
    // GET /teams
    public function index(Request $request)
    {
        $user = $request->user();

        // أي عضو يشوف الفرق اللي هو عضو فيها
        $teams = $user->teams()->withCount('users', 'tasks')
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($teams as $team) {
            $team['doneTasksCount'] = $team->tasks()->where('status', 'done')->count();
        }

        return response()->json([
            'data' => $teams
        ]);
    }

    public function numbersData(Team $team, Request $request)
    {
        $this->authorize('numbersData', $team);

        $user = $request->user();

        if ($team->isAdmin($user->id)) {
            $data['join_requests_count'] = $team->joinRequests()->count();
        }
        $data['tasks_count'] = $team->tasks()->count();
        $data['member_count'] = $team->users()->count();
        $data['team_join_code'] = $team->join_code;


        return response()->json([
            'data' => $data
        ]);
    }

    // POST /teams
    public function store(Request $request)
    {
        $user = $request->user();

        $allowed = $user->adminTeams()->count() < $user->plan->max_created_teams;


        if (!$allowed) {
            return response()->json([
                'message' => 'You have reached your team creation limit. Please upgrade your plan.',
                'limit_reached' => true,
            ], 403); // 403 = Forbidden
        }


        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'description' => 'nullable|string|max:1000'
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => $user->id,
            'join_code' => Str::upper(Str::random(6)),
        ]);

        // attach creator as admin first
        $team->users()->attach($user->id, ['role' => 'admin']);

        // then create chat
        $chat = Chat::create([
            'team_id' => $team->id,
            'type' => 'team',
        ]);

        $chat->participants()->sync($team->users->pluck('id'));

        event(new TeamEvents());

        return response()->json([
            'data' => $team
        ], 201);
    }

    // PUT /teams/{team}
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string|max:1000'
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null
        ]);
        event(new TeamEvents());

        return response()->json([
            'data' => $team
        ]);
    }

    // GET /teams/{team}
    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $team->load('users');
        $team->load('tasks');

        return response()->json([
            'data' => $team
        ]);
    }


    public function checkCode(Request $request)
    {
        $request->validate([
            'join_code' => 'required|string',
        ]);

        $team = Team::where('join_code', $request->join_code)->first();

        if (! $team) {
            return response()->json([
                'message' => 'Invalid team code',
            ]);
        }

        return response()->json([
            'message' => 'Team found',
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'members' => $team->users->count()
            ]
        ]);
    }

    // POST /teams/{team}/join-request
    public function requestToJoin(Request $request, Team $team)
    {
        $user = $request->user();

        $this->authorize('requestToJoin', $team);

        // already requested
        $exists = TeamJoinRequest::where([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status'  => 'pending'
        ])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Join request already sent'
            ], 409);
        }

        TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status'  => 'pending'
        ]);

        return response()->json([
            'message' => 'Join request sent'
        ], 201);
    }

    // DELETE /teams/{team}
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();
        event(new TeamEvents());
        return response()->noContent();
    }

    public function summarizeUserTeams(Request $request)
    {
        $user = $request->user();

        // Get Latest teams with counts of users and tasks
        $latestTeams = $user->teams()
            ->withCount('users', 'tasks')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        foreach ($latestTeams as $team) {
            $team['doneTasksCount'] = $team->tasks()->where('status', 'done')->count();
        }

        return response()->json([
            'data' => $latestTeams,
        ]);
    }

    public function teamMember(Team $team)
    {

        $this->authorize('teamMember', $team);

        $teamMembers = $team->users()->get();
        return response()->json([
            'data' => $teamMembers,
        ]);
    }

    public function teamJoinRequests(Team $team)
    {
        $this->authorize('teamJoinRequests', $team);
        $joinRequests = $team->joinRequests()->get();
        return response()->json([
            'data' => $joinRequests,
        ]);
    }

    public function checkUserRole(Request $request, Team $team)
    {
        $user = $request->user();
        return $team->isAdmin($user->id);
    }

    public function removeUser(Request $request, Team $team)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userIdToRemove = $validated['user_id'];

        // استخدم policy
        $this->authorize('removeUser', [$team, $userIdToRemove]);

        // شيل المستخدم
        $team->users()->detach($userIdToRemove);

        event(new TeamEvents());
        return response()->json([
            'message' => 'User removed from the team successfully.'
        ], 200);
    }

    public function teamChatMembers(Request $request, Team $team)
    {
        $userId = $request->user()->id;

        $members = $team->users()
            ->where('users.id', '!=', $userId)
            ->get([
                'users.id as id',
                'users.name',
                'users.avatar'
            ]);

        $members->transform(function ($member) use ($userId) {

            // get private chat id
            $privateChatId = DB::table('chats')
                ->where('type', 'private')
                ->whereExists(function ($q) use ($userId) {
                    $q->select(DB::raw(1))
                        ->from('chat_user')
                        ->whereColumn('chat_id', 'chats.id')
                        ->where('user_id', $userId);
                })
                ->whereExists(function ($q) use ($member) {
                    $q->select(DB::raw(1))
                        ->from('chat_user')
                        ->whereColumn('chat_id', 'chats.id')
                        ->where('user_id', $member->id);
                })
                ->value('id');

            if ($privateChatId) {
                // unread count
                $member->unread_count = DB::table('messages')
                    ->where('chat_id', $privateChatId)
                    ->where('user_id', '!=', $userId)
                    ->whereNotExists(function ($q) use ($userId) {
                        $q->select(DB::raw(1))
                            ->from('message_reads')
                            ->whereColumn('message_reads.message_id', 'messages.id')
                            ->where('message_reads.user_id', $userId);
                    })
                    ->count();

                // last message time (THIS is the WhatsApp magic)
                $member->last_message_at = DB::table('messages')
                    ->where('chat_id', $privateChatId)
                    ->max('created_at');
            } else {
                $member->unread_count = 0;
                $member->last_message_at = null;
            }

            return $member;
        });

        // WhatsApp-style ordering
        $members = $members
            ->sortByDesc(fn($m) => $m->last_message_at)
            ->values();

        return response()->json([
            'members' => $members
        ]);
    }



    public function globalChatUnread(Request $request, Team $team)
    {
        $userId = $request->user()->id;

        $globalChat = DB::table('chats')
            ->where('team_id', $team->id)
            ->where('type', 'team') // نوع الـ global chat
            ->first();

        $unreadCount = 0;
        if ($globalChat) {
            $unreadCount = DB::table('messages')
                ->where('chat_id', $globalChat->id)
                ->where('user_id', '!=', $userId)
                ->whereNotExists(function ($q) use ($userId) {
                    $q->select(DB::raw(1))
                        ->from('message_reads')
                        ->whereColumn('message_reads.message_id', 'messages.id')
                        ->where('message_reads.user_id', $userId);
                })
                ->count();
        }

        return response()->json([
            'chat_id' => $globalChat?->id,
            'unread_count' => $unreadCount
        ]);
    }
}
