<?php

namespace App\Providers;

use App\Models\Destination;
use App\Listeners\StoreImageDimensions;
use App\Support\ContentCache;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Event;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->app->scoped('nav.destinations', function () {
            try {
                return ContentCache::remember('nav-destinations', 3600, fn () => Destination::published()->get(['id', 'slug', 'name']));
            } catch (\Throwable) {
                return new \Illuminate\Database\Eloquent\Collection(); // keep error pages working when the database is down
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(MediaHasBeenAddedEvent::class, StoreImageDimensions::class);

        // Form posts: a few per minute and a daily cap per visitor.
        RateLimiter::for('inquiries', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perDay(40)->by($request->ip()),
        ]);

        // Destination links shown in the menu and footer.
        View::composer(['partials.navbar', 'partials.footer'], function ($view) {
            $view->with('navDestinations', app('nav.destinations'));
        });
    }
}
