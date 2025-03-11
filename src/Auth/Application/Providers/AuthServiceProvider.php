<?php

namespace Src\Auth\Application\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Src\Auth\Application\Repositories\AuthRepository;
use Src\Auth\Domain\AuthInterface;
use Gate;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [


    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);

    }
}
