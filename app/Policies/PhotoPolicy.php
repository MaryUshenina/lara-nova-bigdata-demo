<?php

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;


class PhotoPolicy
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


    public function create(User $user)
    {
//        return false; //todo fix: not author can add photos if HasMany::make('Photos'),
        return $user->isEstate();
    }

    public function update(User $user, Photo $photo)
    {
        return $user->isAdmin() || $user->isAuthor($photo->ad);
    }

    public function delete(User $user, Photo $photo)
    {
        return $user->isAdmin() || $user->isAuthor($photo->ad);
    }

}
