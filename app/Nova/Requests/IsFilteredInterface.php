<?php

namespace App\Nova\Requests;

use Laravel\Nova\Http\Requests\NovaRequest;

interface IsFilteredInterface
{
    public static function isAnyFilterApplied(NovaRequest $request);
}
