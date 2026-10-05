<?php

namespace App\Providers;

use App\Models\Destination;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Loaded at most once per request (menu and footer both use it).
        $this->app->scoped('nav.destinations', fn () => Destination::published()->get(['id', 'slug', 'name']));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Destination links shown in the menu and footer.
        View::composer(['partials.navbar', 'partials.footer'], function ($view) {
            $view->with('navDestinations', app('nav.destinations'));
        });
    }
}
