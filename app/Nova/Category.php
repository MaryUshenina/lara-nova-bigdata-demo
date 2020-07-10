<?php

namespace App\Nova;

use App\Models\CompiledTreeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Category extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\CompiledTreeCategory::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    public static $perPageOptions = [10];

    public static $perPageViaRelationship = 10;

    private static $allCategoriesOptions = [];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            $this->getParentField($request),

            $this->getNameField($request),

            HasMany::make(__('Children categories'), 'childrenCategories', Category::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }


    public static function indexQuery(NovaRequest $request, $query)
    {
        $query
            ->when($request->isResourceIndexRequest() && !$request->viaRelationship(), function ($q) use ($request) {
                return $q->rootLevel();
            });
    }

    protected static function applyOrderings($query, array $orderings)
    {
        return $query->orderByTree();
    }

    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        //switch to original model
        self::$model = \App\Models\Category::class;
        $model = $model->originalCategory ?? new self::$model();

        $fillFields = parent::fillFields($request, $model, $fields);

        //switch back to CompiledTreeCategory
        self::$model = \App\Models\CompiledTreeCategory::class;
        return $fillFields;
    }


    /**
     * @param Request $request
     * @return Select
     */
    private function getParentField(Request $request)
    {
        if (!count(self::$allCategoriesOptions) && !$request->isResourceIndexRequest()) {
            $all = CompiledTreeCategory::getRawDataArray(true);

            self::$allCategoriesOptions = [0 => 'root'] + $all; // dont use array_merge to keep keys
        }

        return Select::make(__('Parent'), 'pid')
            ->options(self::$allCategoriesOptions)
            ->rules('required', Rule::notIn([$this->id ?? -1]))
            ->onlyOnForms()
            ->sortable();
    }

    /**
     * @param Request $request
     * @return Text
     */
    private function getNameField(Request $request)
    {
        return Text::make(__('Name'), 'name')
            ->displayUsing(function () use ($request) {
                return $request->isResourceIndexRequest() ? $this->tree_name : $this->name;
            })
            ->rules('required', 'max:255')
            ->asHtml()
            ->sortable();

    }
}
