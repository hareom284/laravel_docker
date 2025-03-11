<?php

use Illuminate\Support\Facades\Route;
use Src\Company\UserManagement\Presentation\API\UserMobileController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'user'
], function () {
    Route::get('salesperson', [UserMobileController::class, 'getSalepersonList']);
    Route::get('{id}', [UserMobileController::class, 'findUserInfoById']);
    Route::put('update-profile/{id}', [UserMobileController::class, 'updateProfile']);
    Route::post('survey', [UserMobileController::class, 'survey']);
});
Route::group([
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('get_survey', [UserMobileController::class, 'getSurvey']);
});
Route::put('update-device-id',[UserMobileController::class, 'updateDeviceId']);

