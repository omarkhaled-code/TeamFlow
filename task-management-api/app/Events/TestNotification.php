<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestNotification implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return ['test-channel'];
    }

    public function broadcastAs()
    {
        return 'test.event';
    }
}


// <?php

// namespace App\Events;

// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

// class JoinRequestCreated implements ShouldBroadcast
// {
//     public string $message;
//     public string $time;
//     public $joinRequest;

//     public function __construct($joinRequest)
//     {
//         $this->message ="New join request, and this is teamId ". $joinRequest->team;
//         $this->time = now()->toDateTimeString();
//         $this->joinRequest = $joinRequest;
//     }

//     public function broadcastOn()
//     {
//         return new PrivateChannel(
//             'admin.' . $this->joinRequest->team->id
//         );
//     }

//     public function broadcastAs()
//     {
//         return 'join.request.created';
//     }
// }

