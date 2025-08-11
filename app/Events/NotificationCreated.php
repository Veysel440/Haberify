<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public int $userId, public array $payload) {}

    public function broadcastOn(): array
    { return [new PrivateChannel("notifications.{$this->userId}")]; }

    public function broadcastAs(): string
    { return 'NotificationPushed'; }

    public function broadcastWith(): array
    { return $this->payload; }
}
