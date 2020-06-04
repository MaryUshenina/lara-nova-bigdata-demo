<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes;
    
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const PLAIN_USER_ROLE = 'plain';
    const ESTATE_USER_ROLE = 'estate';
    const ADMIN_USER_ROLE = 'admin';

    const ROLES = [
        self::PLAIN_USER_ROLE => 'Plain User',
        self::ESTATE_USER_ROLE => 'Estate Agent',
        self::ADMIN_USER_ROLE => 'Admin',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function isAdmin()
    {
        return $this->role == self::ADMIN_USER_ROLE;
    }

    public function isEstate()
    {
        return $this->role == self::ESTATE_USER_ROLE;
    }

    public function isAuthor(Ad $ad)
    {
        return $this->id == $ad->user_id;
    }
}
