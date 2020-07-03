<?php

namespace App\Nova\Metrics;

use App\Cache\CacheCallbackInterface;
use App\Cache\CacheCallbackTrait;
use App\Models\Ad;
use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Square1\NovaMetrics\CustomValue;

class AdsTopAgent extends CustomValue implements CacheCallbackInterface
{

    use CacheCallbackTrait;

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
            $filterKey = self::FILTER_ALL;
        }

        $data = self::getCalculatedData($filterKey, $model);
        $agentName = $data->name ?? 'no agent';

        return $this->result($data->count ?? 0)->prefix("$agentName - ")->suffix('ad');
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
        if ($filterKey == self::FILTER_ALL) {
            return User::join('agents_data', 'users.id', 'agents_data.user_id')
                ->select([
                    'users.name',
                    'agents_data.ads_count as count'
                ])->orderBy('agents_data.ads_count', 'desc')
                ->first();
        }

        return self::getCachedOrRetrieve($filterKey, function ($parameters) {
            list($model) = $parameters;

            $topAgent = $model
                ->select('user_id')
                ->addSelect(\DB::raw('COUNT(id) as count'))
                ->groupBy('user_id')
                ->orderByRaw('COUNT(id) desc')
                ->first();

            return (object)[
                'name' => $topAgent->user->name ?? 'no agent',
                'count' => $topAgent->count ?? 0,
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
        return 'ads-top-agent';
    }
}
