<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use DigitalCreative\RangeInputFilter\RangeInputFilter;

class PriceRangeFilter extends RangeInputFilter
{
    public $name = 'Price';

    public function __construct()
    {
        $this->defaults['filterOnSingleParamExists'] = true;
    }

    public function apply(Request $request, $query, $value)
    {
        return $query->when(isset($value['from']), function ($q) use ($value) {
            return $q->where('price', '>=', $value['from']);
        })
            ->when(isset($value['to']), function ($q) use ($value) {
                return $q->where('price', '<=', $value['to']);
            });
    }

}
