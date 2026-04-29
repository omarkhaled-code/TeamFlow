<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCreated extends Notification
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database']; // يخزن في جدول notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'team_id' => $this->task->team_id,
            'title' => 'Task Created',
            'message' => "A new task '{$this->task->title}' has been created in team '{$this->task->team->name}'",
            'created_at_relative' => now()->diffForHumans(),
        ];
    }
}
