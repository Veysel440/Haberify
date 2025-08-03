<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function getProfile(int $userId): User
    {
        return User::findOrFail($userId);
    }

    public function updateProfile(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
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
