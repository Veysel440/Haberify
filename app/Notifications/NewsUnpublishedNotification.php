<?php

namespace App\Notifications;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewsUnpublishedNotification extends Notification
{
    use Queueable;

    public News $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Haberiniz yayından kaldırıldı.',
            'news_id' => $this->news->id,
            'title'   => $this->news->title,
        ];
    }
}
