<?php

namespace App\Events;


use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class JoinRequestCreated implements ShouldBroadcast
{
    public $joinRequest;
    public string $message;
    public string $time;

    public function __construct($joinRequest)
    {
        $this->joinRequest = $joinRequest;
        $this->message = 'New join request';
        $this->time = now()->toDateTimeString();
    }

    public function broadcastOn()
    {
        return ['request-channel'];
    }

    public function broadcastAs()
    {
        return 'request.event';
    }
}