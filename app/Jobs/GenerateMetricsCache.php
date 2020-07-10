<?php

namespace App\Jobs;

use App\Jobs\CacheGenerating\AdsAvailabilityWithNoFilterCache;
use App\Jobs\CacheGenerating\AdsPriceWithNoFilterCache;
use App\Jobs\CacheGenerating\NewAdsCache;
use App\Jobs\CacheGenerating\NewUsersCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMetricsCache implements ShouldQueue
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
        dispatch(new AdsAvailabilityWithNoFilterCache());
        dispatch(new AdsPriceWithNoFilterCache());
        dispatch(new NewAdsCache());
        dispatch(new NewUsersCache());

    }
}
