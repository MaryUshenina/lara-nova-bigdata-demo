<?php

namespace App\Nova\Requests;

use Laravel\Nova\Http\Requests\NovaRequest;

trait IsFilteredTrait
{
    /**
     * check if any filter was applied
     *
     * @param  NovaRequest  $request
     * @return bool
     */
    public static function isAnyFilterApplied(NovaRequest $request)
    {
        if ($request->has('filters')) {
            // Get the decoded list of filters
            $filters = json_decode(base64_decode($filterKey = $request->filters)) ?? [];

            foreach ($filters as $filter) {
                if (empty($filter->value)) {
                    continue;
                }
                return true;
            }
        }

        return false;
    }
}
