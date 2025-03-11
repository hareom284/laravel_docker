<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Notification\Presentation\API\NotificationMobileController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'notifications'
], function () {
    Route::put('read/{id}',[NotificationMobileController::class, 'makeRead']);
    Route::get('app-notifications', [NotificationMobileController::class, 'getAppNoti']);
    Route::get('get-noti-status',[NotificationMobileController::class, 'getNotiStatus']);
});


