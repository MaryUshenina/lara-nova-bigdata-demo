<?php

namespace App\Nova\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Nova;

trait SplitDatesAggregateValueTrait
{


    protected function aggregateSplit($request, $model, $function, $column = null, $dateColumn = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $column = $column ?? $query->getModel()->getQualifiedKeyName();

        $timezone = Nova::resolveUserTimezone($request) ?? $request->timezone;

        $dateColumn = $dateColumn ?? $query->getModel()->getCreatedAtColumn();

        $previousRange = $this->previousRange($request->range, $timezone);
        $previousValue = round(with(clone $query)
            ->whereBetween($dateColumn, [$previousRange[0]->format('Y-m-d'), $previousRange[1]->format('Y-m-d')])
            ->{$function}($column), $this->precision);

        $currentRange = $this->currentRange($request->range, $timezone);
        return $this->result(
            round(with(clone $query)
                ->whereBetween($dateColumn, [$currentRange[0]->format('Y-m-d'), $currentRange[1]->format('Y-m-d')])
                ->{$function}($column), $this->precision)
        )->previous($previousValue);
    }
}
