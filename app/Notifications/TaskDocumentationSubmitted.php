<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskDocumentationSubmitted extends Notification
{
    use Queueable;

    public function __construct(protected Task $task)
    {
    }

    public function via(): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task Documentation Submitted')
            ->line('New documentation has been submitted for task: ' . $this->task->title)
            ->action('View Task', route('tasks.show', $this->task))
            ->line('Please review the documentation and update the task status.');
    }

    public function toArray(): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'submitted_by' => $this->task->assignedTo->name,
        ];
    }
}
