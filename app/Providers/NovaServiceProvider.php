<?php

namespace App\Providers;


use App\Models\EagerCategory;

use App\Nova\Metrics\NewAds;
use App\Nova\Metrics\NewUsers;

use App\Observers\EagerCategoryObserver;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

use \App\Nova\Category;
use \App\Nova\Ad;

class NovaServiceProvider extends NovaApplicationServiceProvider
{

    protected $namespace = 'App\Nova\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::middleware(['nova'])
            ->prefix('nova-api')
            ->namespace($this->namespace)
            ->group(function(){

                Route::get(Category::uriKey(), 'CategoryResourceIndexController@handle');
                Route::get(Ad::uriKey().'/count', 'AdResourceCountController@show');

            });


        Nova::serving(function () {
            EagerCategory::observe(EagerCategoryObserver::class);
        });

        Nova::style('custom-style', public_path('css/custom.css') );
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            NewUsers::make(),
            NewAds::make(),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
