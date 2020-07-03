<?php

namespace App\Observers;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdObserver
{

    public function creating(Ad $item)
    {
        if (empty($item->user_id)) {
            $item->user_id = Auth::user() ? Auth::user()->id : null;

        }

        $item->created_at_time = Carbon::now()->format('H:i:s');

        Cache::flush();
        $item->user->updateAgentData();

        return true;
    }

    public function updating(Ad $item)
    {
        $item->user->updateAgentData();
        return true;
    }

    public function deleting(Ad $item)
    {
        foreach ($item->photos as $photo) {
            $photo->delete(); // to fire events for children
        }

        Cache::flush();
        $item->user->updateAgentData();

        return true;
    }

}
