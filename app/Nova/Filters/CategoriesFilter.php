<?php

namespace App\Nova\Filters;

use App\Models\EagerCategory;
use Illuminate\Http\Request;


use Angauber\NovaSelect2Filter\NovaSelect2Filter;

class CategoriesFilter extends NovaSelect2Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'nova-select2-filter';

    public $name = 'Categories';

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->join('ads_categories', 'ads_categories.ad_id', '=', 'ads_meta.ad_id')
            ->whereIn('ads_categories.category_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        return EagerCategory::getRawDataArray(true);
    }


}
