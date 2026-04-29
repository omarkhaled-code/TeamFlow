<?php

use App\Models\Team;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // return true; // إذا وصل الطلب هنا والـ token صحيح، سيعمل 100%
    // return $user->chats->contains($chat_id);
    return $user !== null;
});

Broadcast::channel('chat.{teamId}', function ($user, $teamId) {
    // return true; // إذا وصل الطلب هنا والـ token صحيح، سيعمل 100%
    // return $user->chats->contains($chat_id);
    return $user !== null;
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    // تأكد إن المستخدم عضو في الفريق
    return $user->teams()->where('teams.id', $teamId)->exists()
        ? [
            'id' => $user->id,
            'name' => $user->name,
        ]
        : false;
});


Broadcast::channel('admin.channel', function ($user, $teamId) {
    // $team = Team::find($teamId);

    // if (! $team) {
    //     return false;
    // }

    // return $team->isAdmin($user->id);
    return true;
});
