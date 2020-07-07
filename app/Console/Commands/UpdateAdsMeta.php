<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\AdMetaData;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateAdsMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:meta:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ad meta data update';

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
        AdMetaData::truncate();

        $limit = 1000;
        $offset = 0;

        $ads = Ad::limit($limit)->get();
        $this->output->progressStart(Ad::count());
        while (count($ads)) {

            foreach ($ads as $ad) {
//                $ad->touch(); // for trigger
                $ad->updateMetaData();
                $this->output->progressAdvance();
            }
            $offset += $limit;
            $ads = Ad::limit($limit)->offset($offset)->get();
        }
        $this->output->progressFinish();

    }
}
