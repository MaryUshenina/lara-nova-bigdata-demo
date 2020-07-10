<?php

namespace App\Nova\Controllers;


use App\Models\Ad as AdModel;
use App\Nova\Ad;
use App\Nova\Requests\IsFilteredInterface;
use App\Nova\Requests\IsFilteredTrait;
use Illuminate\Http\Response;
use Laravel\Nova\Http\Controllers\ResourceCountController;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class AdResourceCountController extends ResourceCountController implements IsFilteredInterface
{
    use IsFilteredTrait;

    /**
     * Get the resource count for a given query.
     *
     * @param  ResourceIndexRequest  $request
     * @return Response
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
