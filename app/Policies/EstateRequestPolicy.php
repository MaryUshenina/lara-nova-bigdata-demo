<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\EstateRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EstateRequestPolicy
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

    public function adminEstateRequest(User $user, EstateRequest $request)
    {
        return $user->isAdmin();
    }

    public function view(User $user, EstateRequest $estateRequest)
    {
        return $user->isAdmin();;
    }

    public function detail(User $user, EstateRequest $estateRequest)
    {
        return $user->isAdmin();;
    }

    public function update(User $user, EstateRequest $estateRequest)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, EstateRequest $estateRequest)
    {
        return false;
    }
}
