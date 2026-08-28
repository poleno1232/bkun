<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function findByName(string $name): \Illuminate\Database\Eloquent\Model|User
    {
        return User::where('name', $name)->firstOrFail();
    }

    public function register(array $data): \Illuminate\Database\Eloquent\Model|User
    {
        return User::create($data);
    }
}
