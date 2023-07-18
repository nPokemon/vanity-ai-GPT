<?php

namespace App\Policies;

use App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserPolicy extends AbstractPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Models\User $target_user): bool
    {
        return true;
    }

    public function create(Authenticatable $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(Authenticatable $user, Models\User $target_user): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(Authenticatable $user, Models\User $target_user): bool
    {
        return $this->isAdmin($user) && $target_user->interviews_count < 1;
    }
}
