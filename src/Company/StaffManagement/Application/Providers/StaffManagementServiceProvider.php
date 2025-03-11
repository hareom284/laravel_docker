<?php

namespace Src\Company\StaffManagement\Application\Providers;

use Illuminate\Support\ServiceProvider;

class StaffManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\StaffManagement\Domain\Repositories\StaffRepositoryInterface::class,
            \Src\Company\StaffManagement\Application\Repositories\Eloquent\StaffRepository::class
        );

        $this->app->bind(
            \Src\Company\StaffManagement\Domain\Repositories\RankRepositoryInterface::class,
            \Src\Company\StaffManagement\Application\Repositories\Eloquent\RankRepository::class
        );

        $this->app->bind(
            \Src\Company\StaffManagement\Domain\Repositories\SalepersonMonthlyKpiRepositoryInterface::class,
            \Src\Company\StaffManagement\Application\Repositories\Eloquent\SalepersonMonthlyKpiRepository::class
        );

        $this->app->bind(
            \Src\Company\StaffManagement\Domain\Repositories\SalepersonYearlyKpiRepositoryInterface::class,
            \Src\Company\StaffManagement\Application\Repositories\Eloquent\SalepersonYearlyKpiRepository::class
        );
    }
}
