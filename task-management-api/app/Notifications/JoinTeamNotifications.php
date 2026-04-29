<?php

namespace App\Notifications;

use App\Models\TeamJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JoinTeamNotifications extends Notification
{
    use Queueable;
    public $teamJoinRequest;

    public function __construct(TeamJoinRequest $teamJoinRequest)
    {
        $this->teamJoinRequest = $teamJoinRequest;
    }

    public function via($notifiable)
    {
        return ['database']; // يخزن في جدول notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->teamJoinRequest->user->id,
            'team_id' => $this->teamJoinRequest->team->id,
            'team_name' => $this->teamJoinRequest->team->name,
            'title' => 'Join Request',
            'message' => "A new request come from '{$this->teamJoinRequest->user->name} to join into '{$this->teamJoinRequest->team->name}' ",
            'created_at_relative' => now()->diffForHumans(),
        ];
    }
}
