<?php

namespace App\Listeners;

use App\Events\ArticlePublished;
use App\Models\User;
use App\Notifications\ArticlePublished as ArticlePublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendArticlePublishedNotifications implements ShouldQueue
{
    public function handle(ArticlePublished $event): void
    {
        User::role('subscriber')->chunkById(500, function($users) use ($event){
            foreach ($users as $u) {
                $u->notify(new ArticlePublishedNotification($event->article));
            }
        });
    }
}
