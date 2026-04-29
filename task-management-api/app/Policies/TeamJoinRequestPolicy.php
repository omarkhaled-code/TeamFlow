<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamJoinRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view a join request (admin only).
     */
    public function view(User $user, TeamJoinRequest $request)
    {
        // يمكن للمسؤول عن الفريق فقط رؤية الطلب
        return $request->team->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }


    /**
     * Determine whether the user can approve a join request (admin only)
     */
    public function approve(User $user, TeamJoinRequest $request)
    {
        return $request->team->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Determine whether the user can reject a join request (admin only)
     */
    public function reject(User $user, TeamJoinRequest $request)
    {
        // return $request->team->users()
        //     ->where('user_id', $user->id)
        //     ->wherePivot('role', 'admin')
        //     ->exists();
        return true;
    }

    /**
     * Determine whether the user can see their own requests
     */
    public function viewOwn(User $user, TeamJoinRequest $request)
    {
        return $request->user_id === $user->id;
    }
}
