<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'pid');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'pid');
    }

}
