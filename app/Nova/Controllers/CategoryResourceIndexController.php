<?php

namespace App\Nova\Controllers;


use App\Nova\Category;
use Illuminate\Support\Collection;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\TrashedStatus;

class CategoryResourceIndexController extends \Laravel\Nova\Http\Controllers\ResourceIndexController
{

    /**
     * List the resources for administration.
     *
     * @param \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ResourceIndexRequest $request)
    {
        $request->route()->setParameter('resource', Category::uriKey());

        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );

        $totalData = new Collection();
        $paginator->getCollection()->map(function($item) use (&$totalData, $request){
            $totalData->add($item);

            $totalData = $totalData->merge(
                $item->childrenCategoriesByRootView()->orderByTree()->get()
            );
        });

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

    /**
     * Get the paginator instance for the index request.
     *
     * @param \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @param string $resource
     * @return \Illuminate\Pagination\Paginator
     */
    protected function paginator(ResourceIndexRequest $request, $resource)
    {
        return $request->toQuery()->simplePaginate(
            $request->viaRelationship()
                ? $resource::$perPageViaRelationship
                : ($request->perPage ?? $resource::perPageOptions()[0])
        );
    }
}
