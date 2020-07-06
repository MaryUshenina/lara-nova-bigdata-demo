<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected $with = ['photos'];

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

}
