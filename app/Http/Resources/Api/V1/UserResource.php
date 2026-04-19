<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public representation of a user.
 *
 * Never return a raw `User` model from an HTTP endpoint — adding a new
 * column to the `users` table would silently leak it to every `/auth/me`
 * consumer. This resource is the explicit allowlist.
 *
 * 2FA status is surfaced as a single boolean derived from the encrypted
 * server-side columns. The ciphertext itself stays on the server.
 *
 * @property-read User $resource
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id' => $user->getKey(),
            'name' => $user->getAttribute('name'),
            'email' => $user->getAttribute('email'),
            'avatar' => $user->getAttribute('avatar'),
            'bio' => $user->getAttribute('bio'),
            'role' => $user->getAttribute('role'),
            'status' => $user->getAttribute('status'),

            // Derived / safe booleans.
            'email_verified' => $user->getAttribute('email_verified_at') !== null,
            'two_factor_enabled' => $user->hasTwoFactorEnabled(),

            // Spatie: roles + permission names only. No ids, no timestamps,
            // no pivot payload — those are implementation details.
            'roles' => $user->getRoleNames()->all(),
            'permissions' => $user->getAllPermissions()->pluck('name')->all(),

            'created_at' => $user->getAttribute('created_at'),
            'updated_at' => $user->getAttribute('updated_at'),
        ];
    }
}
