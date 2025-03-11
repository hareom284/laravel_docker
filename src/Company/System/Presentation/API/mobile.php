<?php

use Illuminate\Support\Facades\Route;
use Src\Company\System\Presentation\API\CompanyMobileController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'company'
], function () {
    Route::get('index', [CompanyMobileController::class, 'index']);
});

