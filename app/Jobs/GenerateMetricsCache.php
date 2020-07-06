<?php

namespace App\Jobs;

use App\Models\Ad;
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

use \Laravel\Nova\Http\Requests\NovaRequest;

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
        AdsPrices::getCalculatedData('all', Ad::query());
        AdsAvailability::getCalculatedData('all', Ad::query());

        $newAdsMetric = new NewAds();
        foreach($newAdsMetric->ranges() as $range => $rangeLabel){
            $fakeRequest = new NovaRequest();
            $fakeRequest->range = $range;
            $newAdsMetric->getCalculatedDataByRange($range, $fakeRequest);
        }

        $newUsersMetric = new NewUsers();
        foreach($newUsersMetric->ranges() as $range => $rangeLabel){
            $fakeRequest = new NovaRequest();
            $fakeRequest->range = $range;
            $newUsersMetric->getCalculatedDataByRange($range, $fakeRequest);
        }

    }
}
