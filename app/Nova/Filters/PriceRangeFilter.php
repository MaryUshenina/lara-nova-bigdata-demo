<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use DigitalCreative\RangeInputFilter\RangeInputFilter;

class PriceRangeFilter extends RangeInputFilter
{
    public $name = 'Price';

    public $priorityInIndex = 1;

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
            })
            ->when(isset($value['from']) && isset($value['to']), function ($q) use ($value, $calcPriceGroup) {
                $group1= $calcPriceGroup($value['from']);
                $group2= $calcPriceGroup($value['to']);
                if($group1 == $group2){

                }

                //                return $q->where('ads_meta.price_group', '<=', $calcPriceGroup($value['to']))
//                    ->where('ads_meta.price', '<=', $value['to']);
            });
    }

}
