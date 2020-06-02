<?php

namespace App\Observers;

use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class AdObserver
{

    public function creating(Ad $item)
    {
        if (empty($item->user_id)) {
            $item->user_id = Auth::user() ? Auth::user()->id : null;

        }
        return true;
    }


}
