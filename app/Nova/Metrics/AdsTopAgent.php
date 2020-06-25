<?php

namespace App\Nova\Metrics;

use App\Models\Ad;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomValue;

class AdsTopAgent extends CustomValue
{
    public $name = 'Top Agent';

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


        $topAgent = $model
            ->select('user_id')
            ->addSelect(\DB::raw('COUNT(id) as count'))
            ->groupBy('user_id')
            ->orderByRaw('COUNT(id) desc')
            ->first();

        $agentName = $topAgent->user->name ?? 'no agent';
        return $this->result($topAgent->count ?? 0)->prefix("$agentName - ")->suffix('ad');
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
