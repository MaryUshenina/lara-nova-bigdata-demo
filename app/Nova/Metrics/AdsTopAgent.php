<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\Ad;
use App\Models\AdMetaData;
use App\Models\User;
use App\Nova\Metrics\Interfaces\FilteredBuilderMetricsInterface;
use App\Nova\Metrics\Traits\FilteredBuilderMetricsTrait;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomValue;

class AdsTopAgent extends CustomValue implements CacheCallbackInterface, FilteredBuilderMetricsInterface
{

    use CacheCallbackTrait;

    use FilteredBuilderMetricsTrait;

    public $name = 'Top Agent';

    const FILTER_ALL = 'all';
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/3';

    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        list($filterKey, $query) = $this->applyFiltersToQueryBuilder($request, AdMetaData::query());

        $data = self::getCalculatedData($filterKey, $query);
        $agentName = $data->name ?? 'no agent';

        return $this->result($data->count ?? 0)->prefix("$agentName - ")->suffix('ad');
    }


    /**
     * get cached data or calculate and cache
     *
     * @param $filterKey
     * @param Builder $query
     * @return mixed
     */
    public static function getCalculatedData($filterKey, Builder $query)
    {
        if ($filterKey == self::FILTER_ALL) {
            return User::join('agents_data', 'users.id', 'agents_data.user_id')
                ->select([
                    'users.name',
                    'agents_data.ads_count as count'
                ])->orderBy('agents_data.ads_count', 'desc')
                ->first();
        }

        return self::getCachedOrRetrieve($filterKey, function ($parameters) {
            list($query) = $parameters;

            return $query->join('users', 'users.id', 'ads_meta.user_id')
                ->select( [
                     'users.name',
                     \DB::raw('COUNT(distinct ads_meta.ad_id) as count')
                ])
                ->groupBy('user_id')
                ->orderByRaw('COUNT(ads_meta.ad_id) desc')
                ->first();

        }, [$query]);
    }


    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'ads-top-agent';
    }
}
