<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TeamEvents implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return ['team-channel'];
    }

    public function broadcastAs()
    {
        return 'team.events';
    }
}
