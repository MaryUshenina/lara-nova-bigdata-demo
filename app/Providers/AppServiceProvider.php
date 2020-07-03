<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use App\Observers\AdObserver;
use App\Observers\CategoryObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.debug', false)) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Category::observe(CategoryObserver::class);

        Ad::observe(AdObserver::class);
        User::observe(UserObserver::class);
    }
}
