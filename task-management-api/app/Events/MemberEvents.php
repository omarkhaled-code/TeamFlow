<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MemberEvents implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return ['member-channel'];
    }

    public function broadcastAs()
    {
        return 'member.events';
    }
}
