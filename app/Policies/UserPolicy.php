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
        return $authUser->isAdmin() || ($user->id == $authUser->id);
    }

    public function detail(User $authUser, User $user)
    {
        return $authUser->isAdmin() || ($user->id == $authUser->id);
    }

    public function update(User $authUser, User $user)
    {
        return $authUser->isAdmin() || ($user->id == $authUser->id);
    }

    public function delete(User $authUser, User $user)
    {
        // admin user cant delete himself for security reasons
        return $authUser->isAdmin() && ($user->id != $authUser->id);
    }
}
