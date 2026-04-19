<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'status', 'avatar', 'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * These cover:
     *  - credential material (password, remember_token)
     *  - 2FA material — encrypted on disk but must never leave the server
     *    in any form; exposing ciphertext widens the offline attack surface
     *    and the presence/absence of the secret itself is a privacy signal.
     *  - moderation internals (comment ban columns) that are server-only;
     *    UIs should receive a derived flag via `UserResource` if needed.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'is_comment_banned',
        'comment_banned_until',
        'comment_ban_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'comment_banned_until' => 'datetime',
            'is_comment_banned' => 'boolean',
        ];
    }

    /**
     * Whether the user has completed the 2FA enrolment flow (i.e. both
     * scanned the QR code and verified at least one code).
     *
     * Exposed as a boolean derivative so `UserResource` can surface 2FA
     * status without leaking any of the encrypted material behind it.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return ! empty($this->getAttribute('two_factor_secret'))
            && $this->getAttribute('two_factor_confirmed_at') !== null;
    }
}
