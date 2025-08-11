<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WeeklyDigest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $topArticles) {}

    public function via($notifiable): array
    { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)->subject('Haberify Haftalık Özet');
        foreach ($this->topArticles as $a) {
            $mail->line($a['title'].' – '.$a['url']);
        }
        return $mail;
    }
}
