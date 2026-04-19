<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

/**
 * Single source of truth for "what abilities does this user's API token get?".
 *
 * Previously inlined twice in TwoFactorController. Centralising the rule
 * keeps role → ability mapping consistent and lets us evolve abilities
 * (or migrate off Sanctum altogether) without grepping controllers.
 */
final readonly class SanctumTokenIssuer
{
    public function __construct(private ConfigRepository $config) {}

    /**
     * Mint a fresh API token for the given user with role-appropriate abilities.
     */
    public function issueFor(User $user): string
    {
        return $user->createToken($this->tokenName(), $this->abilitiesFor($user))->plainTextToken;
    }

    /**
     * @return list<string>
     */
    private function abilitiesFor(User $user): array
    {
        return $this->isAdmin($user)
            ? $this->adminAbilities()
            : $this->defaultAbilities();
    }

    private function isAdmin(User $user): bool
    {
        // spatie/laravel-permission is a hard dependency (see composer.json),
        // so the `HasRoles` trait is always present on User. We still fall
        // back to the column-based `role` attribute first so legacy rows
        // with `role='admin'` but no assigned roles stay recognised.
        if (($user->getAttribute('role') ?? null) === 'admin') {
            return true;
        }

        return (bool) $user->hasRole('admin');
    }

    private function tokenName(): string
    {
        /** @var string */
        return $this->config->get('twofactor.token.name', 'api');
    }

    /**
     * @return list<string>
     */
    private function adminAbilities(): array
    {
        /** @var list<string> */
        return $this->config->get('twofactor.token.admin_abilities', ['*']);
    }

    /**
     * @return list<string>
     */
    private function defaultAbilities(): array
    {
        /** @var list<string> */
        return $this->config->get('twofactor.token.default_abilities', [
            'articles:read',
            'comments:create',
            'me:read',
        ]);
    }
}
