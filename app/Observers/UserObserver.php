<?php

namespace App\Observers;

use App\Models\User;
use Carbon\Carbon;

class UserObserver
{
    public function created(User $item)
    {
        $item->created_at_time = Carbon::now()->format('H:i:s');
        $item->save();
        return true;
    }

    public function deleting(User $item)
    {
        foreach ($item->ads as $ad) {
            $ad->delete(); // to fire events for children
        }

        // delete calculated agent data
        if($item->user->agentData){
            $item->user->agentData()->remove();
        }

        return true;
    }

}
