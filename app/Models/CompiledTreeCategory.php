<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompiledTreeCategory extends Model
{
    protected $table = 'categories_tree_view';

    protected $appends = ['tree_name'];

    public static function getRawDataArray($withTreeIndent, $asArray = true)
    {
        $query = DB::table('categories_tree_view')
            ->select(
                'id',
                $withTreeIndent ? DB::raw("CONCAT(repeat('-', max_level),' ', name) tree_name") : 'name'
            )
            ->orderBy('tree_order');

        if ($asArray) {
            return $query->pluck($withTreeIndent ? 'tree_name' : 'name', 'id')->toArray();
        }

        return $query->get();
    }

    public function getTreeNameAttribute()
    {
        $indent = '';
        if ($this->max_level) {
            $indent = str_repeat('-', $this->max_level)." ";
        }
        return $indent.$this->name;
    }

    public function scopeOrderByTree($query)
    {
        return $query->orderBy('tree_order', 'asc');
    }

    public function scopeRootLevel($query)
    {
        return $query->where('min_pid', 0);
    }

    public function childrenCategories()
    {
        return $this->hasMany(CompiledTreeCategory::class, 'pid', 'id');
    }

    public function childrenCategoriesByRootView()
    {
        return $this->hasMany(CompiledTreeCategory::class, 'min_pid', 'id');
    }

    public function originalCategory()
    {
        return $this->belongsTo(Category::class, 'id', 'id');
    }

}
