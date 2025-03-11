<?php

namespace Src\Company\System\Application\Providers;


use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\System\Domain\Repositories\UserRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \Src\Company\System\Domain\Repositories\CompanyRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\CompanyRepository::class
        );

        $this->app->bind(
            \Src\Company\System\Domain\Repositories\SiteSettingRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\SiteSettingRepository::class
        );
        $this->app->bind(
            \Src\Company\System\Domain\Repositories\GeneralSettingRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\GeneralSettingRepository::class
        );

        $this->app->bind(
            \Src\Company\System\Domain\Repositories\CompanyKpiRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\CompanyKpiRepository::class
        );

        $this->app->bind(
            \Src\Company\System\Domain\Repositories\WhatsappRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\WhatsappRepository::class
        );

        $this->app->bind(
            \Src\Company\System\Domain\Repositories\AccountingSettingRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\AccountingSettingRepository::class
        );
        // Mobile
        $this->app->bind(
            \Src\Company\System\Domain\Repositories\CompanyMobileRepositoryInterface::class,
            \Src\Company\System\Application\Repositories\Eloquent\CompanyMobileRepository::class
        );

    }
}
