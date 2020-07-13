<?php

namespace App\Nova\Filters;

use DigitalCreative\RangeInputFilter\RangeInputFilter;
use Illuminate\Http\Request;

class PriceRangeFilter extends RangeInputFilter
{

    public function __construct()
    {
        $this->defaults['filterOnSingleParamExists'] = true;
    }

    public function apply(Request $request, $query, $value)
    {
        if (is_object($value)) {
            $value = (array)$value;
        }

        $calcPriceGroup = function ($price) {
            return ceil($price / 10000);
        };

        return $query->when(isset($value['from']), function ($q) use ($value, $calcPriceGroup) {
            return $q->where('ads_meta.price_group', '>=', $calcPriceGroup($value['from']))
                ->where('ads_meta.price', '>=', $value['from']);
        })
            ->when(isset($value['to']), function ($q) use ($value, $calcPriceGroup) {
                return $q->where('ads_meta.price_group', '<=', $calcPriceGroup($value['to']))
                    ->where('ads_meta.price', '<=', $value['to']);
            });
    }


    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return __( 'filters.price');
    }

}
