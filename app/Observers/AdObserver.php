<?php

namespace App\Observers;

use App\Jobs\GenerateMetricsCache;
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

        return true;
    }

    public function saved(Ad $item){

        if ($item->user) {
            $item->user->updateAgentData();
        }
        $item->updateMetaData();

        dispatch(new GenerateMetricsCache());

        return true;
    }

    public function deleting(Ad $item)
    {
        foreach ($item->photos as $photo) {
            $photo->delete(); // to fire events for children
        }

        Cache::flush();
        if($item->user) {
            $item->user->updateAgentData();
        }
        if($item->metaData) {
            $item->metaData->delete();
        }

        dispatch(new GenerateMetricsCache());

        return true;
    }

}
