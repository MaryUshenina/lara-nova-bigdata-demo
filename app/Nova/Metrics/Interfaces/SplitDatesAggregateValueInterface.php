<?php

namespace App\Nova\Metrics\Interfaces;


interface SplitDatesAggregateValueInterface
{
    public function aggregateSplit($request, $queryOrModel, $function, $column = null, $dateColumn = null);
}
