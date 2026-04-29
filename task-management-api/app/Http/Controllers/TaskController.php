<?php

namespace App\Http\Controllers;

use App\Events\TaskEvents;
use App\Events\TeamEvents;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Validation\Rule;
use Termwind\Components\Raw;

class TaskController extends Controller
{
    use AuthorizesRequests;
    // GET /teams/{team}/tasks
    public function index(Request $request, Team $team)
    {
        $user = $request->user();

        $exists = $team->users()
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            return response()->json([
                'data' => [],
                'error' => 'This Actions is un authorized!'
            ]);
        }

        $tasks = $team->tasks()->get();
        $tasks->load('assigned_to');

        return response()->json([
            'data' => $tasks
        ]);
    }

    public function show(Team $team, Task $task)
    {
        $this->authorize('show', $task);

        return response()->json([
            'data' => $task
        ]);
    }

    // POST /teams/{team}/tasks (admin only)
    public function store(Request $request, Team $team)
    {
        $user = $request->user();
        $task = new Task(['team_id' => $team->id]);

        $this->authorize('create', $task);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tasks')->where(
                    fn($q) =>
                    $q->where('team_id', $team->id)
                )
            ],
            'description' => 'nullable|string',
            'end_date' => 'nullable|date',
            "status" => 'in:todo,in_progress,done'
        ]);


        $validated['user_id'] = $user->id;
        $task = $team->tasks()->create([
            ...$validated,
            'created_by' => $user->id
        ]);

        foreach ($team->users as $member) {
            if ($member->id !== $user->id) {
                $member->notify(new \App\Notifications\TaskCreated($task));
            }
        }

        event(new TeamEvents);
        event(new TaskEvents);

        return response()->json([
            'data' => $task
        ], 201);
    }

    // PUT /tasks/{task} (admin only)
    public function update(Request $request, Team $team, Task $task)
    {

        $this->authorize('update', $task);
        $user = $request->user;


        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            // 'description' => 'sometimes|string',
            'end_date' => 'nullable|date',
        ]);

        $task->update($validated);
        if ($team->users->count() > 1) {
            foreach ($team->users as $member) {
                if ($member->id !== $user->id) {
                    $member->notify(new \App\Notifications\TaskUpdated($task));
                }
            }
        }
        event(new TaskEvents);
        return response()->json([
            'data' => $task
        ]);
    }

    // PATCH /tasks/{task}/status (assignee only)
    public function updateStatus(Request $request, Team $team, Task $task)
    {
        $user = $request->user();


        $this->authorize('updateStatus', $task);



        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $task->update([
            'status' => $validated['status'],
            'assigned_to' => $user->id
        ]);
        if ($team->users->count() > 0) {

            foreach ($team->users as $member) {
                if ($member->id !== $user->id) {
                    $member->notify(new \App\Notifications\TaskUpdated($task));
                }
            }
        }

        event(new TeamEvents);
        event(new TaskEvents);
        return response()->json([
            'data' => $task
        ]);
    }

    // DELETE /tasks/{task} (admin only)
    public function destroy(Team $team, Task $task)
    {
        $this->authorize('delete', $task);

        // أرسل إشعار لكل أعضاء الفريق قبل الحذف
        foreach ($task->team->users as $member) {
            $member->notify(new \App\Notifications\TaskDeleted($task));
        }


        $task->delete();
        event(new TeamEvents);
        event(new TaskEvents);

        return response()->noContent();
    }
}
