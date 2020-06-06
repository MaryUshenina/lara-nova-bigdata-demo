<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view(User $authUser, User $user)
    {
        return $authUser->isAdmin() || $this->isEqUsers($authUser, $user);
    }

    public function detail(User $authUser, User $user)
    {
        return $authUser->isAdmin() || $this->isEqUsers($authUser, $user);
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->isAdmin() || $this->isEqUsers($authUser, $user);
    }

    public function delete(User $authUser, User $user)
    {
        // admin user cant delete himself for security reasons
        return $authUser->isAdmin() && !$this->isEqUsers($authUser, $user);
    }

    private function isEqUsers(User $authUser, User $user)
    {
        return $user->id == $authUser->id;
    }
}
