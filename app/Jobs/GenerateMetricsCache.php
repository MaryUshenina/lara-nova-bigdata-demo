<?php

namespace App\Jobs;

use App\Models\Ad;
use App\Nova\Metrics\AdsAvailability;
use App\Nova\Metrics\AdsPrices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
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
        AdsPrices::getCalculatedData('all', Ad::query());
        AdsAvailability::getCalculatedData('all', Ad::query());
    }
}
