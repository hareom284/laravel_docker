<?php

namespace Src\Company\Security\Application\Providers;


use Illuminate\Support\ServiceProvider;
use Src\Company\Security\Application\Repositories\Eloquent\NotificationRepository;
use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;
use Src\Company\Security\Application\Repositories\Eloquent\SecurityRepository;
use Src\Company\Security\Domain\Repositories\NotificationRepositoryInterface;
class SecurityServiceProvider extends ServiceProvider
{



    public function register()
    {
        $this->app->bind(
            SecurityRepositoryInterface::class,
            SecurityRepository::class
        );

        $this->app->bind(
           NotificationRepositoryInterface::class,
           NotificationRepository::class
        );
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
