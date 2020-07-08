<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\AdMetaData;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UpdateAdsMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:meta:update {--reset=0}';

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

        $limit = 500;

        DB::table('ads')->whereNull('deleted_at')
            ->select('id')
            ->orderBy('id')
            ->where('id', '>=', $countMeta)
            ->chunk($limit, function ($ads) use ($limit) {

                $ids = $ads->pluck('id');

                DB::table('ads_meta')->insertUsing(
                    ['ad_id', 'user_id', 'country', 'created_at_ymd', 'end_date_ymd', 'price', 'price_group'],
                    function (Builder $query) use ($limit, $ids) {
                        $query->select([
                            'ads.id', 'ads.user_id', 'ads.country',
                            Db::raw("CONCAT(
                            right(YEAR(ads.created_at_date), 2),
                            LPAD(MONTH(ads.created_at_date), 2, 0),
                            LPAD(Day(ads.created_at_date), 2, 0)
                        )"),
                            Db::raw("CONCAT(
                            right(YEAR(ads.end_date), 2),
                            LPAD(MONTH(ads.end_date), 2, 0),
                            LPAD(Day(ads.end_date), 2, 0)
                        )"),
                            'ads.price',

                            DB::raw('CEIL (ads.price / 10000) AS price_group')
                        ])->from('ads')
                            ->leftJoin('ads_meta', function ($join) {
                                $join->on('ads_meta.ad_id', '=', 'ads.id')
                                    ->whereNull('ads.deleted_at');
                            })
                            ->whereNull('ads_meta.ad_id')
                            ->whereIn('id', $ids)
                            ->limit($limit);
                    }
                );

                $this->output->progressAdvance($limit);

            });

        $this->output->progressFinish();
    }
}
