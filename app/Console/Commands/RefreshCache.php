<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMetricsCache;
use Illuminate\Console\Command;

class RefreshCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:custom-cache:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh cached custion data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch_now(new GenerateMetricsCache());
    }
}
