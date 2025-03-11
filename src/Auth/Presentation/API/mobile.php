<?php

use Illuminate\Support\Facades\Route;
use Src\Auth\Presentation\API\SocialAuthController;
use Src\Auth\Presentation\API\AuthController;


Route::group(['prefix' => 'auth'], function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider'])
        ->where('provider', implode('|', config('services.allowed_providers')));  // Dynamically restrict to allowed providers

    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
        ->where('provider', implode('|', config('services.allowed_providers')));  // Dynamically restrict to allowed providers


    Route::post('/create/customer',[AuthController::class,'createCustomer']);
});
