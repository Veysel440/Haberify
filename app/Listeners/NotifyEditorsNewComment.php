<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CommentSubmitted;
use App\Models\User;
use App\Notifications\NewCommentSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Permission\Models\Role;

class NotifyEditorsNewComment implements ShouldQueue
{
    public function handle(CommentSubmitted $event): void
    {
        // Guard against the case where neither role has been seeded yet.
        // `User::role([...])` throws RoleDoesNotExist if any named role
        // is missing, which would crash the whole comment-create pipeline.
        $roles = Role::query()
            ->whereIn('name', ['editor', 'admin'])
            ->pluck('name')
            ->all();

        if ($roles === []) {
            return;
        }

        User::role($roles)->chunkById(500, function ($users) use ($event): void {
            foreach ($users as $u) {
                $u->notify(new NewCommentSubmitted($event->article, $event->comment));
            }
        });
    }
}
