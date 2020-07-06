<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstateRequest extends Model
{
    protected $table = 'estate_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];

    const NEW_STATUS = 'new';
    const CONFIRMED_STATUS = 'confirmed';
    const DECLINED_STATUS = 'declined';

    const STATUSES = [
        self::NEW_STATUS => 'New',
        self::CONFIRMED_STATUS => 'Confirmed',
        self::DECLINED_STATUS => 'Declined',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
