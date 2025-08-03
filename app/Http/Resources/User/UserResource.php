<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'email'  => $this->email,
            'avatar' => $this->avatar ? url('storage/' . $this->avatar) : null,
            'bio'    => $this->bio,
            'role'   => $this->role,
            'status' => $this->status,
        ];
    }
}
