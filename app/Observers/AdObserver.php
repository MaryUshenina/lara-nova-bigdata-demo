<?php

namespace App\Observers;

use App\Models\Ad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdObserver
{

    public function creating(Ad $item)
    {
        if (empty($item->user_id)) {
            $item->user_id = Auth::user() ? Auth::user()->id : null;

        }
        Cache::flush();
        return true;
    }

    public function deleting(Ad $item)
    {
        foreach ($item->photos as $photo) {
            $photo->delete(); // to fire events for children
        }

        Cache::flush();
        return true;
    }

}
