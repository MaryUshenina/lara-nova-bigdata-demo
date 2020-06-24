<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Relations\Pivot;

class AdsCategoryPivot extends Pivot
{
    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            if ($category = Category::find($item->attributes['category_id'] ?? 0)) {
                $item->pivotParent->categories()->syncWithoutDetaching($category->parentCategories);
            }
        });

        static::deleted(function ($item) {
            if ($category = Category::find($item->attributes['category_id'] ?? 0)) {
                $item->pivotParent->categories()->detach($category->childrenCategories);
            }
        });
    }
}
