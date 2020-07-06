<?php

namespace App\Nova\Metrics\Traits;


use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

trait FilteredBuilderMetricsTrait
{

    public function applyFiltersToQueryBuilder(NovaRequest $request, Builder $query)
    {
        $filterKey = 'all';
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
                $model = (new $filter->class)->apply($request, $query, $filter->value);
            }
        }
        if (!$appliedFilters) {
            $filterKey = 'all';
        }

        return [$filterKey, $query];
    }

}
