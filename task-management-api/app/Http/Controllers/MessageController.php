<?php

namespace App\Http\Controllers;

use App\Events\MessageCreated;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // لازم تكون موجودة
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use AuthorizesRequests;

    public function getGlobalChatMessages(Request $request, Team $team)
    {

        $userID = $request->user()->id;
        $chat = $team->chats()->where('type', 'team')->first();
        $messages = $chat->load('messages.user:id,name,avatar');
        if (!$chat->team->hasUser($userID)) {
            return response()->json([
                "your not able to see team chat!"
            ], 403);
        }

        return response()->json([
            'chat' => $chat,
            'messages' => $messages
        ]);
    }

    public function getPrivateChatMessage(Request $request, Team $team)
    {

        $userID = $request->user()->id;
        $chat = $team->chats()->where('type', 'private')->get();

        $messages = $chat->isNotEmpty()
            ? $chat->load('messages.user:id,name,avatar')
            : [];

        // $messages = $chat->load('messages.user:id,name,avatar');
        if (!$chat->team->hasUser($userID)) {
            return response()->json([
                "your not able to see team chat!"
            ], 403);
        }

        return response()->json([
            'chat' => $chat,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $userID = $request->user()->id;

        if (!$chat->team->hasUser($userID)) {
            return response()->json([
                "your not able to see team chat!"
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);
        $message = $chat->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'read_at' => null,

        ]);
        MessageRead::create([
            'message_id' => $message->id,
            'user_id' => $userID,
            'read_at' => now(),
        ]);


        // broadcast to participants via Reverb (or Pusher)
        $message->load('user:id,name,avatar');

        event(new MessageSent($message, $chat->id));
        event(new MessageCreated($chat->team->id));



        return response()->json($message);
    }

    public function markAsRead(Request $request, Chat $chat)
    {
        $userId = $request->user()->id;


        $messages = Message::where('chat_id', $chat->id)
            ->where('user_id', '!=', $userId)
            ->pluck('id');

        foreach ($messages as $messageId) {
            MessageRead::updateOrCreate(
                [
                    'message_id' => $messageId,
                    'user_id' => $userId,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        // // تحديث كل الرسائل الغير مقروءة من الطرف الآخر في هذا الشات
        // DB::table('messages')
        //     ->where('chat_id', $chat->id)
        //     ->where('user_id', '!=', $userId) // فقط رسائل الطرف الآخر
        //     ->whereNull('read_at')
        //     ->update(['read_at' => now()]);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
