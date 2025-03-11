<?php

use Illuminate\Support\Facades\Route;
use Src\Company\CustomerManagement\Presentation\API\CustomerMobileController;
use Src\Company\CustomerManagement\Presentation\API\IdMilestoneController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'customer'
], function () {
    Route::get('', [CustomerMobileController::class, 'getCustomerLists']);
    Route::get('/detail/{id}', [CustomerMobileController::class, 'customerDetail']);
    Route::post('store', [CustomerMobileController::class, 'store']);
    Route::put('update/{id}', [CustomerMobileController::class, 'update']);
    Route::get('/user/lead-with-properties', [CustomerMobileController::class, 'getLeadWithProperties']);
    Route::put('inactive/{id}', [CustomerMobileController::class, 'inactiveCustomer']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'id_milestone'
], function () {
    Route::get('index', [IdMilestoneController::class, 'index']);
});