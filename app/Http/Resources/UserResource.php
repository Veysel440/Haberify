<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'email'  => $this->email,
            'avatar' => $this->avatar ? url('storage/'.$this->avatar) : null,
            'bio'    => $this->bio,
            'role'   => $this->role,
            'status' => $this->status,
        ];
    }
}
