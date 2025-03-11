<?php

namespace Src\Company\CompanyManagement\Application\Providers;


use Illuminate\Support\ServiceProvider;

class ManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\CompanyManagement\Domain\Repositories\FAQItemRepositoryInterface::class,
            \Src\Company\CompanyManagement\Application\Repositories\Eloquent\FAQItemRepository::class
        );

        $this->app->bind(
            \Src\Company\CompanyManagement\Domain\Repositories\BankInfoRepositoryInterface::class,
            \Src\Company\CompanyManagement\Application\Repositories\Eloquent\BankInfoRepository::class
        );

        $this->app->bind(
            \Src\Company\CompanyManagement\Domain\Repositories\QboExpenseTypeRepositoryInterface::class,
            \Src\Company\CompanyManagement\Application\Repositories\Eloquent\QboExpenseTypeRepository::class
        );
    }
}
