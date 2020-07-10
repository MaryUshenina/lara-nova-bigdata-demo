<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\AdMetaData;
use App\Nova\Metrics\Interfaces\FilteredBuilderMetricsInterface;
use App\Nova\Metrics\Traits\FilteredBuilderMetricsTrait;
use DateInterval;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomPartitionValue;

class AdsPrices extends CustomPartitionValue implements CacheCallbackInterface, FilteredBuilderMetricsInterface
{

    use CacheCallbackTrait;

    use FilteredBuilderMetricsTrait;


    public $name = 'Prices';

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

        return $this->result(self::getCalculatedData($filterKey, $query));
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
        return self::getCachedOrRetrieve($filterKey, function ($parameters) {
            list($query) = $parameters;

            $result = $query
                ->addSelect(DB::raw('min(price) as min_price'))
                ->addSelect(DB::raw('max(price) as max_price'))
                ->addSelect(DB::raw('avg(price) as avg_price'))
                ->first();

            $label = ', $';

            return [
                "Min{$label}" => $result->min_price,
                "Max{$label}" => $result->max_price,
                "Avg{$label}" => $result->avg_price
            ];
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
        return 'ads-prices';
    }
}
