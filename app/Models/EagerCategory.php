<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EagerCategory extends Model
{
    protected $table = 'categories_tree_view';

    public function scopeOrderByTree($query)
    {
        return $query->orderBy('tree_order', 'asc');;
    }

    public function scopeRootLevel($query)
    {
        return $query->where('min_pid', 0);
    }

    public function childrenCategories()
    {
        return $this->hasMany(EagerCategory::class, 'pid', 'id');
    }

    public function childrenCategoriesByRootView()
    {
        return $this->hasMany(EagerCategory::class, 'min_pid', 'id');
    }

    public function originalCategory()
    {
        return $this->belongsTo(Category::class, 'id', 'id');
    }



}
