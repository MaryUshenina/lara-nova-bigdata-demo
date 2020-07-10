<?php

namespace App\Nova;


use App\Nova\Actions\DeclineOrRevokeEstateAccess;
use App\Nova\Actions\GrandEstateAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use function __;

class EstateRequest extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\EstateRequest::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];


    /**
     * Get the fields displayed by the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            BelongsTo::make(__('User'), 'user', User::class)
                ->readonly(),

            $this->getStatusField(),

            $this->getCommentField(),
        ];
    }

    /**
     * @return Select
     */
    private function getStatusField()
    {
        return Select::make(__('Status'), 'status')->options(self::$model::STATUSES)
            ->displayUsingLabels()
            ->sortable()
            ->rules('required', Rule::in(array_keys(self::$model::STATUSES)));
    }

    /**
     * @return Textarea
     */
    private function getCommentField()
    {
        return Textarea::make(__('Comment'), 'comment')
            ->alwaysShow()
            ->rules('max:255');
    }

    /**
     * Get the cards available for the request.
     *
     * @param  Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [

            (new GrandEstateAccess())
                ->canSeeWhen('adminEstateRequest', $this)
            ,
            (new DeclineOrRevokeEstateAccess())
                ->canSeeWhen('adminEstateRequest', $this)
            ,

        ];
    }

    public static function availableForNavigation(Request $request)
    {
        return Auth::user()->isAdmin();
    }


    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Estate Agent Requests');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Estate Agent Request');
    }

}
