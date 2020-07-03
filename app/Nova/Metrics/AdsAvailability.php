<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\Ad;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomValue;

class AdsAvailability extends CustomValue implements CacheCallbackInterface
{
    use CacheCallbackTrait;

    public $name = 'Availability';

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

        return $this->result(self::getCalculatedData($filterKey, $model))->suffix('%')->withoutSuffixInflection();
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

            list($model, $filterKey) = $parameters;

            if (class_exists(AdsCount::class) && is_callable(array(AdsCount::class, 'getCalculatedData'))) {
                $total = AdsCount::getCalculatedData($filterKey, $model);
            } else {
                $total = self::getCachedOrRetrieve($filterKey, function ($parameters) {
                    list($model) = $parameters;
                    return $model->count();
                }, [$model], null, get_class($model));
            }

            if (!$total) {
                return 0;
            }
            $available = $model->available()->count();

            return 100 * $available / $total;
        }, [$model, $filterKey], Carbon::now()->endOfDay());

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
        return 'ads-availability';
    }
}
