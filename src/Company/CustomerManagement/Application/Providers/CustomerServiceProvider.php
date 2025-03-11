<?php

namespace Src\Company\CustomerManagement\Application\Providers;


use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\CustomerRepository::class
        );

        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\IdMilestoneRepository::class
        );

        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryMobileInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\IdMilestoneRepositoryMobile::class
        );

        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\RejectedReasonRepository::class
        );

        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\CheckListRepositoryInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\CheckListRepository::class
        );

        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\ReferrerFormRepository::class
        );

        // for Mobile
        $this->app->bind(
            \Src\Company\CustomerManagement\Domain\Repositories\CustomerMobileRepositoryInterface::class,
            \Src\Company\CustomerManagement\Application\Repositories\Eloquent\CustomerMobileRepository::class
        );
    }
}
