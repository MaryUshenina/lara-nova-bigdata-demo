<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public $pid;

    public function parentCategories()
    {
        return $this->belongsToMany(Category::class, 'categories_tree', 'child_id', 'parent_id')
            ->withPivot('level');
    }

    public function childrenCategories()
    {
        return $this->belongsToMany(Category::class, 'categories_tree', 'parent_id', 'child_id')
            ->wherePivot('level', '=', 1);
    }

}
