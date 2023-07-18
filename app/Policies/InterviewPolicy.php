<?php

namespace App\Policies;

use App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class InterviewPolicy extends AbstractPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Models\Interview $interview): bool
    {
        return $this->isAdmin($user) || ($this->isUser($user) && $this->isInterviewee($user, $interview));
    }

    public function create(Authenticatable $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(Authenticatable $user, Models\Interview $interview): bool
    {
        return $this->isAdmin($user) && $interview->isStarted(false);
    }

    public function runAction(Authenticatable $user, Models\Interview $interview): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(Authenticatable $user, Models\Interview $interview): bool
    {
        return $this->isAdmin($user) && $interview->isInvitationSent(false);
    }

    public function isInterviewee(Models\User $user, Models\Interview $interview): bool
    {
        return $interview->user_id === $user->id;
    }
}
