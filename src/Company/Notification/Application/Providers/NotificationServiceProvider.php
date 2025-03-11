<?php

namespace Src\Company\Notification\Application\Providers;


use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
   public function register()
   {
        $this->app->bind(
            \Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface::class,
            \Src\Company\Notification\Application\Repositories\Eloquent\NotificationRepository::class
        );

        $this->app->bind(
            \Src\Company\Notification\Domain\Repositories\NotificationRepositoryMobileInterface::class,
            \Src\Company\Notification\Application\Repositories\Eloquent\NotificationMobileRepository::class
        );
   }
}
