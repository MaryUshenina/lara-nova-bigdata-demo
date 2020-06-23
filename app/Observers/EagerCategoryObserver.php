<?php

namespace App\Observers;


use App\Models\EagerCategory;


class EagerCategoryObserver
{

    public function deleting(EagerCategory $item)
    {
        if ($model = $item->originalCategory) {
            $model->delete();
        }

        return false;
    }
}
