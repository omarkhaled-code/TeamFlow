<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    public function create(User $user, Chat $chat)
    {
        return $chat->team->hasUser($user->id);
    }
    public function show(User $user, Chat $chat)
    {
        return $chat->team->hasUser($user->id);
    }
}
