<?php

namespace Src\Company\UserManagement\Application\Providers;

use Illuminate\Support\ServiceProvider;

class UserManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Src\Company\UserManagement\Domain\Repositories\RoleRepositoryInterface::class,
            \Src\Company\UserManagement\Application\Repositories\Eloquent\RoleRepository::class
        );

        $this->app->bind(
            \Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface::class,
            \Src\Company\UserManagement\Application\Repositories\Eloquent\UserRepository::class
       );

        $this->app->bind(
                \Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface::class,
                \Src\Company\UserManagement\Application\Repositories\Eloquent\UserRepositoryMobile::class
        );

        $this->app->bind(
            \Src\Company\UserManagement\Domain\Repositories\PermissionRepositoryInterface::class,
            \Src\Company\UserManagement\Application\Repositories\Eloquent\PermissionRepository::class
        );

        $this->app->bind(
            \Src\Company\UserManagement\Domain\Repositories\TeamRepositoryInterface::class,
            \Src\Company\UserManagement\Application\Repositories\Eloquent\TeamRepository::class
        );
    }
}
