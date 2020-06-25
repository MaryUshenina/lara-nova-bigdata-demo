<?php

namespace App\Nova\Metrics;

use App\Models\Ad;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomValue;

class AdsAvailability extends CustomValue
{
    public $name = 'Availability';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $model = Ad::make();

        if ($request->has('filters')) {
            // Get the decoded list of filters
            $filters = json_decode(base64_decode($request->filters)) ?? [];

            foreach ($filters as $filter) {
                if (empty($filter->value)) {
                    continue;
                }
                // Create a new instance of the filter and apply the query to your model
                $model = (new $filter->class)->apply($request, $model, $filter->value);
            }
        }

        $total = $model->count();
        if(!$total){
            return $this->result(0);
        }
        $available = $model->available()->count();
        return $this->result(100 * $available/$total)->suffix('%')->withoutSuffixInflection();
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
