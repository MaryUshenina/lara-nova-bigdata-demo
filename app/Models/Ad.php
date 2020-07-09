<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ad extends Model
{

    use SoftDeletes;

    protected $table = 'ads';

    const CREATED_AT = 'created_at_date';

    protected $fillable = [
        'title', 'description', 'phone', 'country_id', 'email', 'end_date'
    ];


    protected $casts = [
        'end_date' => 'date',
        'created_at_date' => 'date',
        'created_at_time' => 'date',
    ];

    protected $with = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function photos()
    {
        return $this->hasMany(Photo::class, 'ad_id', 'id')->orderby('order');
    }

    public function categories()
    {
        return $this->belongsToMany(EagerCategory::class, 'ads_categories', 'ad_id', 'category_id')
            ->using(AdsCategoryPivot::class);
    }

    public function scopeAvailable($query)
    {
        return $query->whereRaw('end_date > now()');
    }

    public function metaData()
    {
        return $this->hasOne(AdMetaData::class, 'ad_id', 'id');
    }

    /***
     * update related meta Data
     */
    public function updateMetaData()
    {
        AdMetaData::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'ad_id'=> $this->id,
        ],
        [
            'ad_id'=> $this->id,
            'user_id' => $this->user_id,
            'country' => $this->country,
            'created_at_ymd' => $this->created_at_date->format('ymd'),
            'end_date_ymd' => $this->end_date->format('ymd'),
            'price' => $this->price,
            'price_group' => ceil($this->price / 10000),
        ]);

    }

    /**
     * @return int|mixed
     */
    public static function getTotalCountWithoutFiltersViaAgentsData()
    {
        return DB::table('agents_data')
                ->select(DB::raw('sum(ads_count) as total'))
                ->first()->total ?? 0;

    }
}
