<?php

namespace App\Nova\Metrics;

use App\Models\Ad;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomPartitionValue;

class AdsPrices extends CustomPartitionValue
{

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

        $label = ', $';
        return $this->result([
            "Min{$label}" => $model->min('price'),
            "Max{$label}" => $model->max('price'),
            "Avg{$label}" => $model->avg('price')
        ])->colors([
            "Min{$label}" => '#000',
            "Max{$label}" => '#000',
            "Avg{$label}" => '#000'
        ]);

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
