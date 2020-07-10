<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\Ad;
use App\Models\AdMetaData;
use App\Nova\Metrics\Interfaces\FilteredBuilderMetricsInterface;
use App\Nova\Metrics\Traits\FilteredBuilderMetricsTrait;
use DateInterval;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomValue;

class AdsCount extends CustomValue implements CacheCallbackInterface, FilteredBuilderMetricsInterface
{

    use CacheCallbackTrait;

    use FilteredBuilderMetricsTrait;

    const FILTER_ALL = 'all';


    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/5';

    /**
     * Calculate the value of the metric.
     *
     * @param  NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        list($filterKey, $query) = $this->applyFiltersToQueryBuilder($request, AdMetaData::query());

        return $this->result(self::getCalculatedData($filterKey, $query))->suffix('ad');
    }

    /**
     * get cached data or calculate and cache
     *
     * @param $filterKey
     * @param  Builder  $query
     * @return mixed
     */
    public static function getCalculatedData($filterKey, Builder $query)
    {
        if ($filterKey == self::FILTER_ALL) {
            return Ad::getTotalCountWithoutFiltersViaAgentsData();
        }
        return self::getCachedOrRetrieve($filterKey, function ($parameters) {
            list($query) = $parameters;
            return $query->count();
        }, [$query], null, get_class($query));
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
     * @return  DateTimeInterface|DateInterval|float|int
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
        return 'ads-count';
    }

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return __( 'Ads count');
    }
}
