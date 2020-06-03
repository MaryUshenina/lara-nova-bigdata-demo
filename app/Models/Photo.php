<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }


}
