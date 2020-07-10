<?php

namespace App\Policies;


use App\Models\CompiledTreeCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompiledTreeCategoryPolicy
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

    public function view(User $user, CompiledTreeCategory $category)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, CompiledTreeCategory $category)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CompiledTreeCategory $category)
    {
        return $user->isAdmin();
    }

}
