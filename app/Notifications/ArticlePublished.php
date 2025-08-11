<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ArticlePublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Article $article) {}

    public function via($notifiable): array
    { return ['database','mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Yeni makale yayınlandı: '.$this->article->title)
            ->greeting('Merhaba')
            ->line('Yeni içerik: '.$this->article->title)
            ->action('Görüntüle', url('/news/'.$this->article->slug))
            ->line('Haberify');
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type'   => 'article_published',
            'id'     => $this->article->id,
            'title'  => $this->article->title,
            'slug'   => $this->article->slug,
            'author' => $this->article->author?->name,
        ]);
    }
}
