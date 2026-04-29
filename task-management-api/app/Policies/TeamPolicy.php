<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * أي عضو في الفريق يقدر يشوف الفريق
     */
    public function view(User $user, Team $team)
    {
        return $team->hasUser($user->id);
    }

    public function numbersData(User $user, Team $team)
    {
        return $team->hasUser($user->id);
    }
    /**
     * فقط الأدمن يقدر يعدل الفريق (مثلا الاسم)
     */
    public function update(User $user, Team $team)
    {
        return $team->isAdmin($user->id);
    }

    /**
     * فقط الأدمن يقدر يحذف الفريق
     */
    public function delete(User $user, Team $team)
    {
        return $team->isAdmin($user->id);
    }

    /**
     * أي عضو يمكنه طلب الانضمام لفريق
     */
    public function requestToJoin(User $user, Team $team)
    {
        return !$team->hasUser($user->id);
    }

    /**
     * فقط الأدمن يمكنه الموافقة أو رفض طلب انضمام
     */
    public function handleJoinRequest(User $user, Team $team)
    {
        return $team->isAdmin($user->id);
    }

    /**
     * فقط الأدمن يمكنه إضافة مستخدم مباشرة (attach)
     */
    public function addUser(User $user, Team $team)
    {
        return $team->isAdmin($user->id);
    }

    public function teamMember(User $user, Team $team)
    {
        return $team->hasUser($user->id);
    }

    public function teamJoinRequests(User $user, Team $team)
    {
        return $team->isAdmin($user->id);
    }

    public function joinRequestsSummarize(User $user, Team $team)
    {
        return $team->isAdmin($user->id);
    }

    // هذا للتحقق إن المستخدم يمكنه إزالة عضو
    public function removeUser(User $user, Team $team, $targetUserId)
    {
        // المستخدم الحالي لازم يكون admin
        if (! $team->isAdmin($user->id)) {
            return false;
        }

        // المستخدم اللي عايزين نشيله لازم يكون member عادي
        $isMember = $team->users()
            ->where('user_id', $targetUserId)
            ->wherePivot('role', 'member')
            ->exists();

        return $isMember;
    }
}
