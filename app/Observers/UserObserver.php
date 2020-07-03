<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
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
