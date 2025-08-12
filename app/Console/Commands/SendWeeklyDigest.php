<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyDigestMail;
use App\Models\Article;
use App\Models\User;

class SendWeeklyDigest extends Command
{
    protected $signature = 'mail:weekly-digest';
    protected $description = 'Send weekly digest to subscribers';

    public function handle(): int
    {
        $top = Article::published()
            ->orderByDesc('published_at')->limit(10)
            ->get(['id','slug','title'])
            ->map(fn($a)=>$a->only('id','slug','title'))->all();

        User::role('subscriber')->chunkById(500, function($users) use ($top) {
            foreach ($users as $u) {
                Mail::to($u->email)->queue(new WeeklyDigestMail($top));
            }
        });

        $this->info('Weekly digest queued.');
        return self::SUCCESS;
    }
}
