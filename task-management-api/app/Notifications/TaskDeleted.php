<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDeleted extends Notification
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'team_id' => $this->task->team_id,
            'title' => 'Task Deleted',
            'message' => "The task '{$this->task->title}' has been deleted from team '{$this->task->team->name}'",
            'created_at_relative' => now()->diffForHumans()
        ];
    }
}
