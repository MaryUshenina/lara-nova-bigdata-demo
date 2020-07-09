<?php

namespace App\Nova\Metrics\Traits;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Nova;

trait SplitDatesAggregateValueTrait
{
    public function aggregateSplit($request, $queryOrModel, $function, $column = null, $dateColumn = null, $dateFormat = 'Y-m-d')
    {
        $query = $queryOrModel instanceof Builder ? $queryOrModel : (new $queryOrModel)->newQuery();

        $column = $column ?? $query->getModel()->getQualifiedKeyName();

        $timezone = Nova::resolveUserTimezone($request) ?? $request->timezone;

        $dateColumn = $dateColumn ?? $query->getModel()->getCreatedAtColumn();

        $previousRange = $this->previousRange($request->range, $timezone);
        $previousValue = round(with(clone $query)
            ->whereBetween($dateColumn, [$previousRange[0]->format($dateFormat), $previousRange[1]->format($dateFormat)])
            ->{$function}($column), $this->precision);

        $currentRange = $this->currentRange($request->range, $timezone);
        return $this->result(
            round(with(clone $query)
                ->whereBetween($dateColumn, [$currentRange[0]->format($dateFormat), $currentRange[1]->format($dateFormat)])
                ->{$function}($column), $this->precision)
        )->previous($previousValue);
    }
}
