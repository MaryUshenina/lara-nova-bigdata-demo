<?php

namespace App\Jobs\CacheGenerating;

use App\Models\Ad;
use App\Models\AdMetaData;
use App\Nova\Metrics\AdsAvailability;
use App\Nova\Metrics\AdsPrices;
use App\Nova\Metrics\NewAds;
use App\Nova\Metrics\NewUsers;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use \Laravel\Nova\Http\Requests\NovaRequest;

class AdsAvailabilityWithNoFilterCache implements ShouldQueue
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
         AdsAvailability::getCalculatedData('all', AdMetaData::query());
    }
}
