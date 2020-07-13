<?php

namespace App\Nova;

use App\Nova\Actions\RequestForEstateRole;
use App\Nova\Requests\PostSizeInterface;
use App\Nova\Requests\PostSizeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\PasswordConfirmation;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use function __;

class User extends Resource implements PostSizeInterface
{
    use PostSizeTrait;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

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
    public static $search = [
        'id',
        'name',
        'email',
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
            $this->getAvatarField(),

            $this->getNameField(),

            $this->getEmailField(),

            $this->getPasswordField(),
            $this->getPasswordConfirmField(),

            $this->getRoleField(),

            HasMany::make('Ads')
                ->canSee(function () {
                    return !$this->isPlain();
                }),
        ];
    }

    /**
     * @return Avatar
     */
    private function getAvatarField()
    {
        return Avatar::make(__('Avatar'), 'avatar')
            ->rules("image", "max:".self::getMaxPostSizeInKiloBytes())
            ->disableDownload();
    }

    /**
     * @return Text
     */
    private function getNameField()
    {
        return Text::make('Name')
            ->sortable()
            ->rules('required', 'max:255');
    }

    /**
     * @return Text
     */
    private function getEmailField()
    {
        return Text::make('Email')
            ->sortable()
            ->rules('required', 'email', 'max:254')
            ->creationRules('unique:users,email')
            ->updateRules('unique:users,email,{{resourceId}}');
    }

    /**
     * @return Password
     */
    private function getPasswordField()
    {
        return Password::make('Password')
            ->onlyOnForms()
            ->updateRules('nullable', 'confirmed', 'string', 'min:8');
    }

    /**
     * @return PasswordConfirmation
     */
    private function getPasswordConfirmField()
    {
        return PasswordConfirmation::make(__('Password Confirmation'));
    }

    /**
     * @return Select
     */
    private function getRoleField()
    {
        return Select::make(__('Role'), 'role')->options(self::$model::ROLES)
            ->displayUsingLabels()
            ->onlyOnIndex()
            ->readonly()
            ->sortable()
            ->rules('required', Rule::in(array_keys(self::$model::ROLES)));
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
            (new RequestForEstateRole())
                ->canSee(function ($request) {
                    return $request->user()->can('seeEstateRequestButton', \App\Models\User::class);
                }),
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
        return __('Users');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('User');
    }

}
