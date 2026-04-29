<?php

namespace App\Notifications;

use App\Models\Team;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserJoinedTeam extends Notification
{
    use Queueable;

    public $user; // المستخدم اللي انضم
    public $team; // الفريق اللي انضم ليه

    public function __construct(User $user, Team $team)
    {
        $this->user = $user;
        $this->team = $team;
    }

    // Notification channels
    public function via($notifiable)
    {
        return ['database']; // نخزن في جدول notifications
    }

    // البيانات اللي هتتحفظ في قاعدة البيانات
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'team_id' => $this->team->id,
            'title' => 'User Joined Team',
            'message' => "User '{$this->user->name}' has joined the team '{$this->team->name}'",
            'created_at_relative' => now()->diffForHumans()
        ];
    }
}
