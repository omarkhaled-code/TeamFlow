<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaskEvents implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return ['task-channel'];
    }

    public function broadcastAs()
    {
        return 'task.events';
    }
}
