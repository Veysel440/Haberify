<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Article $article, public Comment $comment) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Yeni yorum bekliyor: ' . $this->article->title)
            ->line('Yorum: "' . $this->comment->body . '"')
            ->action('Yönet', url('/admin/articles/' . $this->article->id . '/comments'));
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'new_comment',
            'article_id' => $this->article->id,
            'comment_id' => $this->comment->id,
            'excerpt' => str($this->comment->body)->limit(120),
        ]);
    }
}
