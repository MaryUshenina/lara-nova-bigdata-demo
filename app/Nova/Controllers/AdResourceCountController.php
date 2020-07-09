<?php

namespace App\Nova\Controllers;


use App\Nova\Ad;
use App\Models\Ad as AdModel;

use App\Nova\Requests\IsFilteredInterface;
use App\Nova\Requests\IsFilteredTrait;
use \Laravel\Nova\Http\Requests\ResourceIndexRequest;

class AdResourceCountController extends \Laravel\Nova\Http\Controllers\ResourceCountController implements IsFilteredInterface
{
    use IsFilteredTrait;

    /**
     * Get the resource count for a given query.
     *
     * @param \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceIndexRequest $request)
    {
        if (!self::isAnyFilterApplied($request)) {
            $count = AdModel::getTotalCountWithoutFiltersViaAgentsData();
        } else {
            $request->route()->setParameter('resource', Ad::uriKey());
            $count = $request->toCount();
        }

        return response()->json(['count' => $count]);
    }


}
