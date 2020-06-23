<?php

namespace App\Nova;

use Illuminate\Http\Request;
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
    public static $model = \App\Models\EagerCategory::class;

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
            $this->getParentField(),

            $this->getNameField(),

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

        //switch back to EagerCategory
        self::$model = \App\Models\EagerCategory::class;
        return $fillFields;
    }


    /**
     * @return Select
     */
    private function getParentField()
    {
        if (!count(self::$allCategoriesOptions)) {
            $all = \App\Models\EagerCategory::select('id')
                ->addSelect(\DB::raw("CONCAT(REPEAT('--', max_level), ' ',`name`) AS `name` "))
                ->orderByTree()
                ->get()
                ->pluck('name', 'id')->toArray();

            self::$allCategoriesOptions = [0 => 'root'] + $all; // dont use array_merge to keep keys
        }

        return Select::make(__('Parent'), 'pid')
            ->options(self::$allCategoriesOptions)
            ->rules('required', Rule::notIn([$this->id ?? -1]))
            ->onlyOnForms()
            ->sortable();
    }

    /**
     * @return Text
     */
    private function getNameField()
    {
        return Text::make(__('Name'), 'name')
            ->displayUsing(function () {

                $indent = '';
                if ($this->max_level) {
                    $indent = str_repeat('--', $this->max_level) . '&nbsp;';
                }
                return $indent . $this->name;
            })
            ->rules('required', 'max:255')
            ->asHtml()
            ->sortable();

    }
}
