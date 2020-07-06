<?php

namespace App\Nova\Metrics\Interfaces;


use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Http\Requests\NovaRequest;

interface FilteredBuilderMetricsInterface
{

    public function applyFiltersToQueryBuilder(NovaRequest $request, Builder $query);

}
