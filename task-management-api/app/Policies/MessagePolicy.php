<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class MessagePolicy
{
    public function create(User $user, Chat $chat)
    {
        return $chat->team->hasUser($user->id);
    }
    public function index(User $user, Chat $chat)
    {
        return $chat->team->hasUser($user->id);
    }
}
