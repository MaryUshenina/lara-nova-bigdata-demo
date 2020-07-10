<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy
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

    public function view(User $user, Ad $ad)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isEstate();
    }

    public function update(User $user, Ad $ad)
    {
        return $user->isAdmin() || $user->isAuthor($ad);
    }


    public function delete(User $user, Ad $ad)
    {
        return $user->isAdmin();
    }

}
