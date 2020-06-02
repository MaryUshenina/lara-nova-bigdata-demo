<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{

    protected $table = 'ads';


    protected $fillable = [
        'title', 'description', 'phone', 'country_id', 'email', 'end_date'
    ];


    protected $casts = [
        'end_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
