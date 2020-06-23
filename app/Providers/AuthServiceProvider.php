<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\EagerCategory;
use App\Models\EstateRequest;
use App\Models\Photo;
use App\Models\User;
use App\Policies\AdPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\EagerCategoryPolicy;
use App\Policies\EstateRequestPolicy;
use App\Policies\PhotoPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Ad::class => AdPolicy::class,
        User::class => UserPolicy::class,
        Photo::class => PhotoPolicy::class,
        EstateRequest::class => EstateRequestPolicy::class,
        Category::class => CategoryPolicy::class,
        EagerCategory::class => EagerCategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
