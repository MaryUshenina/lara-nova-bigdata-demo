<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\Ad;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomPartitionValue;

class AdsPrices extends CustomPartitionValue implements CacheCallbackInterface
{

    use CacheCallbackTrait;


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
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {

        $model = Ad::make();

        $appliedFilters = 0;
        if ($request->has('filters')) {
            // Get the decoded list of filters
            $filters = json_decode(base64_decode($filterKey = $request->filters)) ?? [];

            foreach ($filters as $filter) {
                if (empty($filter->value)) {
                    continue;
                }
                $appliedFilters++;
                // Create a new instance of the filter and apply the query to your model
                $model = (new $filter->class)->apply($request, $model, $filter->value);
            }
        }
        if(!$appliedFilters){
            $filterKey = 'all';
        }


        return $this->result(self::getCalculatedData($filterKey, $model));
    }


    /**
     * get cached data or calculate and cache
     *
     * @param $filterKey
     * @param $model
     * @return mixed
     */
    public static function getCalculatedData($filterKey, $model)
    {
        return self::getCachedOrRetrieve($filterKey, function ($parameters) {
            list($model) = $parameters;

            $result = $model
                ->addSelect(DB::raw( 'min(price) as min_price'))
                ->addSelect(DB::raw( 'max(price) as max_price'))
                ->addSelect(DB::raw( 'avg(price) as avg_price'))
                ->first();

            $label = ', $';

            return [
                "Min{$label}" => $result->min_price,
                "Max{$label}" => $result->max_price,
                "Avg{$label}" => $result->avg_price
            ];
        }, [$model]);
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
        return 'ads-prices';
    }
}
