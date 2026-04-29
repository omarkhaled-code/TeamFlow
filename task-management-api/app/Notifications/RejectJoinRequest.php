<?php

namespace App\Notifications;

use App\Models\TeamJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectJoinRequest extends Notification
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
            'title' => 'Join Request',
            'message' => "Your Join Request is Rejected from admin, And now you aren't member in team '{$this->teamJoinRequest->team->name}! ",
            'created_at_relative' => now()->diffForHumans(),
        ];
    }
}
