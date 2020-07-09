<?php

namespace App\Nova;

use App\Models\AdMetaData;
use App\Models\EagerCategory;
use App\Nova\Metrics\AdsAvailability;
use App\Nova\Metrics\AdsCount;
use App\Nova\Metrics\AdsPrices;
use App\Nova\Metrics\AdsTopAgent;
use App\Nova\Requests\IsFilteredInterface;
use App\Nova\Requests\IsFilteredTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jfeid\NovaGoogleMaps\NovaGoogleMaps;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

use Benjacho\BelongsToManyField\BelongsToManyField;

use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\TextWrap\TextWrap;

use Wemersonrv\InputMask\InputMask;

use Klepak\NovaRouterLink\RouterLink;

use App\Models\Ad as AdModel;

class Ad extends Resource implements IsFilteredInterface
{

    use IsFilteredTrait;

    private static $allCategoriesOptions = [];

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


    public static $perPageOptions = [15];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [

            $this->getCategoryField($request),

            $this->getMainImageField(),

            //title
            $this->getTitleLinkField(),
            $this->getTitleField(),

            $this->getCreatedField(),

            // description
            $this->getDescriptionIndexField(),
            $this->getDescriptionOtherField(),

            $this->getPriceField(),

            $this->getEmailField(),

            $this->getPhoneField(),

            $this->getCountryField(),

            $this->getEndDateField(),

//            HasMany::make('Photos'),

//            NestedForm::make('Photos'),

            $this->getGoogleMapFiled($request),

            $this->getWithoutAddressField()
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
        return [
            AdsCount::make(),
            AdsPrices::make(),
            AdsTopAgent::make(),
            AdsAvailability::make(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\CategoriesFilter,
            new Filters\PriceRangeFilter,
            new Filters\CountryFilter,
            new Filters\AgentFilter,
            new Filters\CreatedAtFilter,
        ];
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


    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        $fillFields = parent::fillFields($request, $model, $fields);

        // first element should be model object
        $modelObject = $fillFields[0];

        if($modelObject->save_without_address){
            $modelObject->location_lat = null;
            $modelObject->location_lng = null;
        }
        unset($modelObject->save_without_address);

        return $fillFields;
    }


    protected static function applyFilters(NovaRequest $request, $query, array $filters)
    {
        if ($filtersApplied = self::isAnyFilterApplied($request)) {
            $query=AdMetaData::query();
//            $query->join('ads_meta', 'ads_meta.ad_id', '=', 'ads.id');
        }else{
            $query=AdModel::query();
        }

        $query = parent::applyFilters( $request, $query, $filters);
        if($filtersApplied){

//            Ad::query()->whereIn()
            $query->join('ads', 'ads_meta.ad_id', '=', 'ads.id')
                ->select('ads.*');
        }

        return $query;
    }

    /**
     * @param Request $request
     * @return BelongsToManyField
     */
    private function getCategoryField(Request $request)
    {
        $isForm = !($request->isResourceIndexRequest() || $request->isResourceDetailRequest());

        if (!count(self::$allCategoriesOptions) && !$request->isResourceIndexRequest()) {
            self::$allCategoriesOptions = EagerCategory::getRawDataArray($isForm, false);
        }

        return BelongsToManyField::make(__('Categories'), 'categories', Category::class)
            ->options(self::$allCategoriesOptions)
            ->optionsLabel($isForm ? 'tree_name' : 'name')
            ->rules('nullable')
            ->hideFromIndex();
    }

    /**
     * @return Image
     */
    private function getMainImageField()
    {
        return Image::make(__('Image'), 'photo')
            ->displayUsing(function () {
                return $this->photo ?? 'no_image.png';
            })
            ->disableDownload();
    }

    /**
     * @return RouterLink
     */
    private function getTitleLinkField()
    {
        return RouterLink::make(__('Title'), 'title')
            ->route('detail',
                [
                    'resourceName' => 'ads',
                    'resourceId' => $this->id,
                ]
            )
            ->withMeta(['value' => $this->title])
            ->onlyOnIndex();
    }

    /**
     * @return Text
     */
    private function getTitleField()
    {
        return Text::make(__('Title'), 'title')
            ->hideFromIndex()
            ->rules('required', 'max:255');

    }

    /**
     * @return Text
     */
    private function getCreatedField()
    {
        $outputFormat = 'm.d.y';

        return Text::make(__('Created'), function () use ($outputFormat) {
            if(is_string($createdAt = $this->created_at_date)){
                $createdAt = Carbon::createFromFormat('Y-m-d', $this->created_at_date);
            }
            return $createdAt->format($outputFormat);
        })
            ->asHtml();
    }

    /**
     *  description for index page
     *
     * @return TextWrap
     */
    private function getDescriptionIndexField()
    {
        return TextWrap::make(__('Description'), 'description')
            ->rules('required', 'max:1000')
            ->displayUsing(function ($str) {
                return Str::limit($str, 255);
            })
            ->wrapMethod('length', 60);
    }

    /**
     * description for other pages
     *
     * @return Textarea
     */
    private function getDescriptionOtherField()
    {
        return Textarea::make(__('Description'), 'description')
            ->alwaysShow()
            ->rules('required', 'max:1000');
    }

    /**
     * @return Text
     */
    private function getPriceField()
    {
        return Text::make(__('Price'), 'price')
            ->hideFromIndex()
            ->rules('required', 'numeric', 'between:0,99999.99', 'regex:/^\d+(\.\d{1,2})?$/');
    }

    /**
     * @return Text
     */
    private function getEmailField()
    {
        return Text::make(__('Email'), 'email')
            ->hideFromIndex()
            ->rules('required', 'email', 'max:254');
    }

    /**
     * @return InputMask
     */
    private function getPhoneField()
    {
        return InputMask::make(__('Phone'), 'phone')
            ->mask('+1 (###) ###-####')
            ->hideFromIndex()
            ->rules('required');
    }

    /**
     * @return Select
     */
    private function getCountryField()
    {
        return Select::make(\__('Country'), 'country')
            ->options(\Countries::getList('en'))
            ->rules('required')
            ->hideFromIndex()
            ->displayUsingLabels();
    }


    /**
     * @return Date
     */
    private function getEndDateField()
    {
        return Date::make(__('End date'), 'end_date')
            ->onlyOnForms()
            ->rules('required', 'date_format:Y-m-d');
    }

    /**
     * @param Request $request
     * @return NovaGoogleMaps
     */
    private function getGoogleMapFiled(Request $request)
    {
        return NovaGoogleMaps::make(__('Address'), 'location')
            ->hideFromIndex()
            ->rules([Rule::requiredIf(function () use ($request) {
                if ($request->location_lat) {
                    return false;
                }
                return !$request->save_without_address;
            })])
            ->hideFromDetail(function () {
                return is_null($this->location_lat) || !$this->location_lat;
            })
            ->setValue($this->location_lat, $this->location_lng);
    }

    /**
     * @return Boolean
     */
    private function getWithoutAddressField()
    {
        return Boolean::make(__('Save without address'), 'save_without_address')
            ->onlyOnForms();
    }
}
