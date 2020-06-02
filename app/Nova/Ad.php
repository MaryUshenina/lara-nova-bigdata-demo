<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

use Treestoneit\TextWrap\TextWrap;

//use Laravel\Nova\Http\Requests\NovaRequest;

class Ad extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Ad::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [

            Text::make(__('Title'), 'title')
                ->rules('required', 'max:255'),

            // description for index page
            TextWrap::make(__('Description'), 'description')
                ->rules('required', 'max:1000')
                ->displayUsing(function ($str) {
                    return Str::limit($str, 255);
                })
                ->wrapMethod('length', 100),

            // description for other pages
            Textarea::make(__('Description'), 'description')
                ->alwaysShow()
                ->rules('required', 'max:1000'),

            //end description

            Text::make(__('Email'), 'email')
                ->onlyOnForms()
                ->rules('required', 'email', 'max:254'),

            Text::make(__('Phone'), 'phone')
                ->onlyOnForms()
                ->rules('required'),

            Select::make(\__('Country'), 'country_id')
                ->options([1 => 'country1', 2 => 'country2'])
                ->rules('required')
                ->onlyOnForms()
                ->displayUsingLabels(),

            Date::make(__('End date'), 'end_date')
                ->onlyOnForms()
                ->rules('required'),

            Text::make(__('Created'), 'created_at')->readonly()
                ->displayUsing(function ($date) {
                    return $date->format('m.d.y h:i a');
                }),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
