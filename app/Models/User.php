<?php

namespace App\Models;

use App\AgentsData;
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

    const ROLES_WITH_ADS = [
        self::ESTATE_USER_ROLE,
        self::ADMIN_USER_ROLE
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

    public function isPlain()
    {
        return $this->role == self::PLAIN_USER_ROLE;
    }

    public function isAuthor(Ad $ad)
    {
        return $this->id == $ad->user_id;
    }

    public function estateRequests()
    {
        return $this->hasMany(EstateRequest::class);
    }

    public function agentData()
    {
        return $this->hasOne(AgentData::class, 'user_id', 'id');
    }



    /**
     * update related calculated Data
     */
    public function updateAgentData()
    {
        if(!in_array($this->role, self::ROLES_WITH_ADS)){
            return;

        }
        if (!$this->agentData) {
            $data = new AgentData();
            $data->user_id = $this->id;
            $data->save();
            $this->refresh();
        }

        $this->agentData->ads_count = $this->ads()->count();
        $this->agentData->save();
    }

}
