<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\User;
use App\Nova\Metrics\Interfaces\SplitDatesAggregateValueInterface;
use App\Nova\Metrics\Traits\SplitDatesAggregateValueTrait;
use DateInterval;
use DateTimeInterface;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class NewUsers extends Value implements SplitDatesAggregateValueInterface, CacheCallbackInterface
{

    use SplitDatesAggregateValueTrait;
    use CacheCallbackTrait;

    /**
     * Calculate the value of the metric.
     *
     * @param  NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->getCalculatedDataByRange($request->range, $request);
    }


    /**
     * @param $filterKey
     * @param $request
     * @return mixed
     */
    public function getCalculatedDataByRange($filterKey, $request)
    {
        return self::getCachedOrRetrieve($filterKey, function ($parameters) {
            list($object, $request) = $parameters;

            return $object->count($request, User::class);

        }, [$this, $request]);
    }


    protected function aggregate($request, $model, $function, $column = null, $dateColumn = null)
    {
        return $this->aggregateSplit($request, $model, $function, $column, $dateColumn);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            365 => '365 Days',
            'TODAY' => 'Today',
            'MTD' => 'Month To Date',
            'QTD' => 'Quarter To Date',
            'YTD' => 'Year To Date',
        ];
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
        return 'new-users';
    }

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return __( 'New users');
    }
}
