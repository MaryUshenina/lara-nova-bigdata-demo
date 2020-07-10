<?php

namespace App\Observers;


use App\Models\CompiledTreeCategory;


class CompiledTreeCategoryObserver
{

    public function deleting(CompiledTreeCategory $item)
    {
        if ($model = $item->originalCategory) {
            $model->delete();
        }

        return false;
    }
}
