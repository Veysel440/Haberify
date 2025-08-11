<?php

namespace App\Listeners;

use App\Events\CommentSubmitted;
use App\Models\User;
use App\Notifications\NewCommentSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEditorsNewComment implements ShouldQueue
{
    public function handle(CommentSubmitted $event): void
    {
        User::role(['editor','admin'])->chunkById(500, function($users) use ($event){
            foreach ($users as $u) {
                $u->notify(new NewCommentSubmitted($event->article, $event->comment));
            }
        });
    }
}
