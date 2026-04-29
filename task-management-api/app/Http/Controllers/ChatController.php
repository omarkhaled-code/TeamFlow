<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Team;
// use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //
public function getOrCreatePrivateChat(Request $request, Team $team)
{
    $authUser = $request->user();

    $validated = $request->validate([
        'other_user_id' => ['required', 'exists:users,id'],
    ]);

    $otherUserId = $validated['other_user_id'];

    // Find existing private chat in THIS team
    $chat = Chat::where('type', 'private')
        ->where('team_id', $team->id)
        ->whereHas('participants', fn ($q) =>
            $q->where('user_id', $authUser->id)
        )
        ->whereHas('participants', fn ($q) =>
            $q->where('user_id', $otherUserId)
        )
        ->first();

    // Create if not exists
    if (! $chat) {
        $chat = Chat::create([
            'type'    => 'private',
            'team_id' => $team->id,
        ]);

        $chat->participants()->sync([
            $authUser->id,
            $otherUserId,
        ]);
    }

    // Load messages AFTER chat exists
    $chat->load('messages.user:id,name,avatar');

    return response()->json([
        'chat'     => $chat,
        'messages' => $chat->messages,
    ]);
}


}
