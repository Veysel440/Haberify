<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

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
