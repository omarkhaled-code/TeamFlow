<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;

class TaskPolicy
{



    // Admin only
    public function create(User $user, Task $task)
    {
        return $task->team->isAdmin($user->id);
    }
    public function show(User $user, Task $task)
    {
        return $task->team->hasUser($user->id);
    }

    // Admin only
    public function update(User $user, Task $task)
    {
        return $task->team->isAdmin($user->id);
    }

    // Assignee only
    // public function updateStatus(User $user, Task $task)
    // {
    //     return $task->team->hasUser($user->id);
    // }

    public function updateStatus(User $user, Task $task)
    {
        return $task->team->hasUser($user->id);
    }


    // Assignee only
    public function index(User $user, Team $team)
    {
        return $team->hasUser($user->id);
    }

    // Admin only
    public function delete(User $user, Task $task)
    {
        return $task->team->isAdmin($user->id);
    }
}
