<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'status', 'avatar', 'bio',
    ];

    /**
     * Credential / 2FA / moderation columns that must never leak to HTTP
     * responses OR to the activity_log audit trail. Duplicated intentionally
     * with `SENSITIVE_ATTRIBUTES` below so the `$hidden` serialization list
     * and the Activitylog exclude list stay derivable from the same
     * canonical source.
     *
     * @var list<string>
     */
    public const SENSITIVE_ATTRIBUTES = [
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = self::SENSITIVE_ATTRIBUTES;

    /**
     * Activitylog configuration.
     *
     * We log every attribute (`logOnly(['*'])`) so any future column gets
     * audit coverage automatically, then explicitly exclude every entry
     * in `SENSITIVE_ATTRIBUTES`. A new sensitive column gets protected in
     * both surfaces (HTTP + audit) with one edit.
     *
     * `logOnlyDirty()` keeps log rows minimal (only changed attributes),
     * and `dontSubmitEmptyLogs()` prevents write events for no-op saves.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['*'])
            ->logExcept(self::SENSITIVE_ATTRIBUTES)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
