<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdMetaData extends Model
{

    protected $table = 'ads_meta';

    protected $primaryKey = 'ad_id';

    protected $fillable = [
        'ad_id',
        'user_id',
        'country',
        'created_at_ymd',
        'end_date_ymd',
        'price',
        'price_group'
    ];

    const CREATED_AT = null;
    const UPDATED_AT = null;


    public function getDeletedAtColumn()
    {
        return null;
    }

}
