<?php

use Illuminate\Support\Facades\Route;
use Src\Company\CompanyManagement\Presentation\API\AccountantController;
use Src\Company\CompanyManagement\Presentation\API\AccountantMobileController;
use Src\Company\CompanyManagement\Presentation\API\FAQItemController;

Route::group([
    'prefix' => 'accountant',
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('get-sale-confirm-amounts/{project_id}', [AccountantMobileController::class, 'getSaleConfirmAmt']);
});
