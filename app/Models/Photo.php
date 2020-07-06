<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    protected $table = 'photos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }


}
