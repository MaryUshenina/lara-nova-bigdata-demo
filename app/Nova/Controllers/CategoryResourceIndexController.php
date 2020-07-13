<?php

namespace App\Nova\Controllers;


use App\Models\CompiledTreeCategory;
use App\Nova\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Controllers\ResourceIndexController;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class CategoryResourceIndexController extends ResourceIndexController
{

    /**
     * List the resources for administration.
     *
     * @param  ResourceIndexRequest  $request
     * @return JsonResponse
     */
    public function handle(ResourceIndexRequest $request)
    {
        // hack to get the right resource
        $request->route()->setParameter('resource', Category::uriKey());

        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );

        $totalData = CompiledTreeCategory::getChildrenGroupsForRootLevel($paginator->getCollection());

        return response()->json([
            'label' => $resource::label(),
            'resources' => $totalData->mapInto($resource)->map->serializeForIndex($request),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'per_page' => $paginator->perPage(),
            'per_page_options' => $resource::perPageOptions(),
            'softDeletes' => $resource::softDeletes(),
        ]);
    }

}
