<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class WeeklyDigestMail extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $articles) {}

    public function build()
    {
        return $this->subject('Haberify Haftalık Özet')
            ->view('emails.weekly_digest');
    }
}
