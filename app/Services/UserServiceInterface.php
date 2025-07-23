<?php

namespace App\Services;

use App\Models\User;

interface UserServiceInterface
{
    public function getProfile(int $userId): ?User;
    public function updateProfile(int $userId, array $data): User;
    public function suspendUser(int $userId): User;
    public function activateUser(int $userId): User;
}
