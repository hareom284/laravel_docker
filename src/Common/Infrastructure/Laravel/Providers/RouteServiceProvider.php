<?php

namespace Src\Common\Infrastructure\Laravel\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {


            // for web route
            // Route::middleware(['web'])
            //     ->group(function () {
            //         require base_path('src/Auth/Presentation/HTTP/routes.php');
            //         require base_path('src/Company/System/Presentation/HTTP/routes.php');
            //         require base_path('src/Company/Security/Presentation/HTTP/routes.php');
            //         require base_path('src/Company/Organization/Presentation/HTTP/routes.php');
            //     });

            // for api route
            Route::middleware('api')
            ->prefix('api')
            ->group(function () {
                require base_path('src/Auth/Presentation/API/routes.php');
                require base_path('src/Company/System/Presentation/API/routes.php');
                require base_path('src/Company/Project/Presentation/API/routes.php');
                require base_path('src/Company/Document/Presentation/API/routes.php');
                require base_path('src/Company/CompanyManagement/Presentation/API/routes.php');
                require base_path('src/Company/Notification/Presentation/API/routes.php');
                require base_path('src/Company/UserManagement/Presentation/API/routes.php');
                require base_path('src/Company/CustomerManagement/Presentation/API/routes.php');
                require base_path('src/Company/StaffManagement/Presentation/API/routes.php');
                require base_path('src/Company/Ecatalog/Presentation/API/routes.php');
            });

            // for mobile api route
            Route::middleware('api')
            ->prefix('mobile-api')
            ->group(function () {
                require base_path('src/Auth/Presentation/API/mobile.php');
                require base_path('src/Company/Project/Presentation/API/mobile.php');
                require base_path('src/Company/Document/Presentation/API/mobile.php');
                require base_path('src/Company/CustomerManagement/Presentation/API/mobile.php');
                require base_path('src/Company/CompanyManagement/Presentation/API/mobile.php');
                require base_path('src/Company/System/Presentation/API/mobile.php');
                require base_path('src/Company/UserManagement/Presentation/API/mobile.php');
                require base_path('src/Company/Notification/Presentation/API/mobile.php');
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(2000)->by($request->ip());
        });
    }
}
