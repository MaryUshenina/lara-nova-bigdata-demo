<?php

namespace App\Jobs\CacheGenerating;

use App\Nova\Metrics\NewUsers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Http\Requests\NovaRequest;

class NewUsersCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $newUsersMetric = new NewUsers();
        foreach ($newUsersMetric->ranges() as $range => $rangeLabel) {
            $fakeRequest = new NovaRequest();
            $fakeRequest->range = $range;
            $newUsersMetric->getCalculatedDataByRange($range, $fakeRequest);
        }

    }
}
