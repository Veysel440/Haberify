<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public function getProfile(int $userId): ?User
    {
        return User::findOrFail($userId);
    }

    public function updateProfile(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        if (isset($data['avatar'])) {
            $user->avatar = $data['avatar'];
        }
        $user->fill($data);
        $user->save();
        return $user;
    }

    public function suspendUser(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->status = 'banned';
        $user->save();
        return $user;
    }

    public function activateUser(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->status = 'active';
        $user->save();
        return $user;
    }
}
