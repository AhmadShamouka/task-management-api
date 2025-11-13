<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommentAdded extends Notification
{
    use Queueable;

    public function __construct(
        public Task $task,
        public Comment $comment
    ) {
    }

    public function via(object $notifiable): array
    {
        // you can also add 'database' if you want DB notifications
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        
        
        
    \Log::info($notifiable);
        $task = $this->task;
        $comment = $this->comment;

        return (new MailMessage)
            ->subject('New comment on your task: ' . $task->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new comment was added to your task.')
            ->line('Task: ' . $task->title)
            ->line('Comment by: ' . $comment->user->name)
            ->line('Comment:')
            ->line($comment->body)
            ->line('Thank you.');
    }
}
