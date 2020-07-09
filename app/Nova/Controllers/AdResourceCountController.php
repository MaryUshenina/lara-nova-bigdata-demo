<?php

namespace App\Nova\Controllers;


use App\Nova\Ad;
use \Laravel\Nova\Http\Requests\ResourceIndexRequest;

class AdResourceCountController extends \Laravel\Nova\Http\Controllers\ResourceCountController
{
    /**
     * Get the resource count for a given query.
     *
     * @param \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceIndexRequest $request)
    {
        if (!$this->isAnyFilterApplied($request)) {
            $count = Ad::getTotalCountWithoutFiltersViaAgentsData();
        } else {
            $request->route()->setParameter('resource', Ad::uriKey());
            $count = $request->toCount();
        }

        return response()->json(['count' => $count]);
    }

    /**
     * check if any filter was applied
     *
     * @param ResourceIndexRequest $request
     * @return bool
     */
    private function isAnyFilterApplied(ResourceIndexRequest $request)
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
