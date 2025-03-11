<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Notification\Presentation\API\NotificationController;


Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'notifications'
], function () {
    Route::get('index', [NotificationController::class, 'index']);
    Route::post('', [NotificationController::class, 'store']);
    Route::delete('delete', [NotificationController::class, 'destroy']);
});


