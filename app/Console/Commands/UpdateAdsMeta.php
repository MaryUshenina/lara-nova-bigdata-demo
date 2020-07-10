<?php

namespace App\Console\Commands;

use App\Models\AdMetaData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateAdsMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ads:meta-update {--reset=0} {--limit=300}';

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
        if ($reset = $this->option('reset')) {
            $this->info('reset data');
            AdMetaData::truncate();
        }

        $this->generateData();
    }

    private function generateData()
    {
        $countAds = DB::table('ads')->selectRaw('MAX(id) as max')->first()->max ?? 0;
        $this->output->progressStart($countAds);

        $countMeta = DB::table('ads_meta')->selectRaw('MAX(ad_id) as max')->first()->max ?? 0;
        $this->output->progressAdvance($countMeta);

        $limit = $this->option('limit');

        DB::table('ads')->whereNull('deleted_at')
            ->select(['id', 'user_id', 'country', 'created_at_date', 'end_date', 'price'])
            ->orderBy('id')
            ->where('id', '>=', $countMeta)
            ->chunk($limit, function ($ads) use ($limit) {

                $insertData = [];
                foreach ($ads as $ad) {
                    $created = Carbon::createFromFormat('Y-m-d', $ad->created_at_date);
                    $endDate = Carbon::createFromFormat('Y-m-d', $ad->end_date);

                    $insertData[] = [
                        'ad_id' => $ad->id,
                        'user_id' => $ad->user_id,
                        'country' => $ad->country,
                        'created_at_ymd' => $created->format('ymd'),
                        'end_date_ymd' => $endDate->format('ymd'),
                        'price' => $ad->price,
                        'price_group' => ceil($ad->price / 10000),
                    ];
                }

                DB::table('ads_meta')->insertOrIgnore($insertData);

                $this->output->progressAdvance($limit);

            });

        $this->output->progressFinish();
    }
}
