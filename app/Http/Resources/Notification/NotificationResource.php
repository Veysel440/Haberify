<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'message'   => $this->message,
            'read'      => (bool)$this->read,
            'created_at'=> $this->created_at->toDateTimeString(),
        ];
    }
}
