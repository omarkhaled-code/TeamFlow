<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageCreated implements ShouldBroadcast
{
    public $teamId;
    

    public function __construct( $teamId)
    {
        $this->teamId = $teamId;
    }

    public function broadcastOn()
    {
        
        return new PrivateChannel('chat.' . $this->teamId);
    }

    public function broadcastAs()
    {
        return 'message.created';
    }
}
