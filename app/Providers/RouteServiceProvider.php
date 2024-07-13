<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {


            Route::middleware(['localization', 'auth:sanctum', 'brand'])
                ->prefix('brand')
                ->group(base_path('routes/brands.php'));

            Route::middleware(['localization', 'auth:sanctum', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/admins.php'));

            Route::middleware(['localization', 'auth:sanctum', 'creator'])
                ->prefix('creator')
                ->group(base_path('routes/creators.php'));

            Route::middleware(['localization', 'auth:sanctum'])
            ->prefix('authed')
                ->group(base_path('routes/authed.php'));

            Route::middleware(['localization', 'api'])
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['localization', 'web'])
                ->group(base_path('routes/web.php'));
        });
    }
}
