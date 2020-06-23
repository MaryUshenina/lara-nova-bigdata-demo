<?php

namespace App\Observers;

use App\Jobs\RedrawTheTree;
use App\Models\EagerCategory;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryObserver
{

    public function created(Category $item)
    {
        //lev 0 - binding category to itself
        $item->parentCategories()->attach($item->id, ['level' => 0]);

        return true;
    }


    public function saved(Category $item)
    {
        if (!is_null($item->pid)) {
            // attach parent
            dispatch_now(new RedrawTheTree($item->id, $item->pid));
        }

        return true;
    }


    public function deleting(Category $item)
    {
        foreach ($item->childrenCategories as $subCat) {
            $subCat->delete(); // to fire events for children
        }
        return true;
    }
}
