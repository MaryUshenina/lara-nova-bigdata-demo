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
        $request->route()->setParameter('resource', Category::uriKey());

        $paginator = $this->paginator(
            $request, $resource = $request->resource()
        );

        $idsLevel0 = [];
        $paginator->getCollection()->map(function ($item) use (&$idsLevel0) {
            $idsLevel0[] = $item->id;
        });

        $allChildren = CompiledTreeCategory::whereIn('min_pid', $idsLevel0)
            ->select('*')
            ->addSelect(DB::raw("CONCAT(repeat('-', max_level),' ', name) tree_name"))
            ->orderByTree()->get();

        $childrenPerRootLevel = [];
        $allChildren->map(function ($item) use (&$childrenPerRootLevel) {
            $childrenPerRootLevel[$item->min_pid][] = $item;
        });

        $totalData = new Collection();
        $paginator->getCollection()->map(function ($item) use (&$totalData, $request, $childrenPerRootLevel) {
            $totalData->add($item);

            if (isset($childrenPerRootLevel[$item->id])) {
                $totalData = $totalData->merge(
                    collect($childrenPerRootLevel[$item->id])
                );
            }
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
     * @param  ResourceIndexRequest  $request
     * @param  string  $resource
     * @return Paginator
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
