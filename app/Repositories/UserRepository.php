<?php

namespace App\Repositories;

use App\Models;
use Laravel\Sanctum\NewAccessToken;

class UserRepository extends AbstractRepository
{
    public function createToken(Models\User $user, string $name = 'default'): NewAccessToken
    {
        return $user->createToken($name);
    }
}
