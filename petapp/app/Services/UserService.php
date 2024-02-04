<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @param array{name: string, email: string, password: string} $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @param array{id: int, name: string, email: string, password: string} $data
     */
    public function updateUser(array $data): bool
    {
        return User::find($data['id'])->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @param array{email: string, password: string} $credentials
     */
    public function loginUser(array $credentials): string|bool
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user->createToken('authToken')->accessToken;
        }

        return false;
    }
}
