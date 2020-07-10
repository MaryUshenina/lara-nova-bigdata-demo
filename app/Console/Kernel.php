<?php

namespace App\Console;

use App\Console\Commands\RefreshCache;
use App\Console\Commands\TestTree;
use App\Console\Commands\UpdateAdsMeta;
use App\Console\Commands\UpdateAgentData;
use App\Jobs\GenerateMetricsCache;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RefreshCache::class,
        TestTree::class,
        UpdateAdsMeta::class,
        UpdateAgentData::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new GenerateMetricsCache)->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
