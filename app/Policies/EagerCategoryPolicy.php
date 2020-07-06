<?php

namespace App\Policies;


use App\Models\EagerCategory;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class EagerCategoryPolicy
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

    public function view(User $user, EagerCategory $category)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, EagerCategory $category)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, EagerCategory $category)
    {
        return $user->isAdmin();
    }

}
