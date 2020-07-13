<?php

namespace App\Nova;

use App\Models\CompiledTreeCategory;
use App\Nova\Metrics\AdsAvailability;
use App\Nova\Metrics\AdsCount;
use App\Nova\Metrics\AdsPrices;
use App\Nova\Metrics\AdsTopAgent;
use App\Nova\Requests\IsFilteredInterface;
use App\Nova\Requests\IsFilteredTrait;
use App\Nova\Requests\PostSizeInterface;
use App\Nova\Requests\PostSizeTrait;
use Benjacho\BelongsToManyField\BelongsToManyField;
use Countries;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jfeid\NovaGoogleMaps\NovaGoogleMaps;
use Klepak\NovaRouterLink\RouterLink;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Treestoneit\TextWrap\TextWrap;
use Wemersonrv\InputMask\InputMask;
use function __;

class Ad extends Resource implements IsFilteredInterface, PostSizeInterface
{

    use IsFilteredTrait;
    use PostSizeTrait;

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
     * @param  Request  $request
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
     * @param  Request  $request
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
     * @param  Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\CategoriesFilter,
            new Filters\AgentFilter,
            new Filters\PriceRangeFilter,
            new Filters\CountryFilter,
            new Filters\CreatedAtFilter,
        ];
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
        return [];
    }


    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        $fillFields = parent::fillFields($request, $model, $fields);

        // first element should be model object
        $modelObject = $fillFields[0];

        if ($modelObject->save_without_address) {
            $modelObject->location_lat = null;
            $modelObject->location_lng = null;
        }
        unset($modelObject->save_without_address);

        return $fillFields;
    }


    protected static function applyFilters(NovaRequest $request, $query, array $filters)
    {
        if (self::isAnyFilterApplied($request)) {
            $query->join('ads_meta', 'ads_meta.ad_id', '=', 'ads.id')
                ->select('ads.*');
        }

        return parent::applyFilters($request, $query, $filters);
    }

    /**
     * @param  Request  $request
     * @return BelongsToManyField
     */
    private function getCategoryField(Request $request)
    {
        $isForm = !($request->isResourceIndexRequest() || $request->isResourceDetailRequest());

        if (!count(self::$allCategoriesOptions) && !$request->isResourceIndexRequest()) {
            self::$allCategoriesOptions = CompiledTreeCategory::getRawDataArray($isForm, false);
        }

        return BelongsToManyField::make(__( 'resource.ads.categories'), 'categories', Category::class)
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
        return Image::make(__('resource.ads.photo'), 'photo')
            ->displayUsing(function () {
                return $this->photo ?? 'no_image.png';
            })
            ->rules("image", "max:".self::getMaxPostSizeInKiloBytes())
            ->disableDownload();
    }

    /**
     * @return RouterLink
     */
    private function getTitleLinkField()
    {
        return RouterLink::make(__('resource.ads.title'), 'title')
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
        return Text::make(__('resource.ads.title'), 'title')
            ->hideFromIndex()
            ->rules('required', 'max:255');

    }

    /**
     * @return Text
     */
    private function getCreatedField()
    {
        return Text::make(__('resource.ads.created'), function () {
            return $this->created_at_date->format('m.d.y');
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
        return TextWrap::make(__('resource.ads.description'), 'description')
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
        return Textarea::make(__('resource.ads.description'), 'description')
            ->alwaysShow()
            ->rules('required', 'max:1000');
    }

    /**
     * @return Text
     */
    private function getPriceField()
    {
        return Text::make(__('resource.ads.price'), 'price')
            ->hideFromIndex()
            ->rules('required', 'numeric', 'between:0,99999.99', 'regex:/^\d+(\.\d{1,2})?$/');
    }

    /**
     * @return Text
     */
    private function getEmailField()
    {
        return Text::make(__('resource.ads.email'), 'email')
            ->hideFromIndex()
            ->rules('required', 'email', 'max:254');
    }

    /**
     * @return InputMask
     */
    private function getPhoneField()
    {
        return InputMask::make(__('resource.ads.phone'), 'phone')
            ->mask('+1 (###) ###-####')
            ->hideFromIndex()
            ->rules('required');
    }

    /**
     * @return Select
     */
    private function getCountryField()
    {
        return Select::make(__('resource.ads.country'), 'country')
            ->options(Countries::getList(config('app.locale')))
            ->rules('required')
            ->hideFromIndex()
            ->displayUsingLabels();
    }


    /**
     * @return Date
     */
    private function getEndDateField()
    {
        return Date::make(__('resource.ads.end_date'), 'end_date')
            ->onlyOnForms()
            ->rules('required', 'date_format:Y-m-d');
    }

    /**
     * @param  Request  $request
     * @return NovaGoogleMaps
     */
    private function getGoogleMapFiled(Request $request)
    {
        return NovaGoogleMaps::make(__('resource.ads.address'), 'location')
            ->hideFromIndex()
            ->rules([
                Rule::requiredIf(function () use ($request) {
                    if ($request->location_lat) {
                        return false;
                    }
                    return !$request->save_without_address;
                })
            ])
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
        return Boolean::make(__('resource.ads.save_without_address'), 'save_without_address')
            ->onlyOnForms();
    }


    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('resource.ads.multiple_label');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('resource.ads.singular_label');
    }

}
