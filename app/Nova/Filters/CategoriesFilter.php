<?php

namespace App\Nova\Filters;

use Angauber\NovaSelect2Filter\NovaSelect2Filter;
use App\Models\CompiledTreeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class CategoriesFilter extends NovaSelect2Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'nova-select2-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  Request  $request
     * @param  Builder  $query
     * @param  mixed  $value
     * @return Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->join('ads_categories', 'ads_categories.ad_id', '=', 'ads_meta.ad_id')
            ->whereIn('ads_categories.category_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return CompiledTreeCategory::getRawDataArray(true);
    }


    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return __( 'filters.categories');
    }

}
