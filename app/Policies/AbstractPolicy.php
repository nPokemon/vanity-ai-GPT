<?php

namespace App\Policies;

use App\Models;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class AbstractPolicy
{
    use HandlesAuthorization;

    protected function isAdmin(Authenticatable $user): bool
    {
        return $user instanceof Models\Admin;
    }

    protected function isUser(Authenticatable $user): bool
    {
        return $user instanceof Models\User;
    }
}
