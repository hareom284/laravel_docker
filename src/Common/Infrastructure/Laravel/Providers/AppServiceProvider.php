<?php

namespace Src\Common\Infrastructure\Laravel\Providers;


use Illuminate\Support\ServiceProvider;
use Src\Auth\Application\Repositories\Eloquent\AuthRepository;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Response;


class AppServiceProvider extends ServiceProvider
{

    public function register()
    {


        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data = null,$message,$status) {
            return Response::json([
              'status'  => true,
              'message' => $message,
              'data' => $data,
            ],$status);
        });

        Response::macro('error', function ($data = null,$message,$status = 400) {
            return Response::json([
              'status'  => "error",
              'message' => $message,
              'data' => $data,
            ],$status);
        });
    }
}
