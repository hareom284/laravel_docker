<?php

use Illuminate\Support\Facades\Route;
use Src\Company\CompanyManagement\Presentation\API\AccountantController;
use Src\Company\CompanyManagement\Presentation\API\FAQItemController;
use Src\Company\System\Presentation\API\SetupController;

Route::group([
    'prefix' => 'accountant',
    'middleware' => 'auth:sanctum',
], function () {
    Route::get('get-projects', [AccountantController::class, 'getProjectLists']);
    Route::get('get-in-progress-projects', [AccountantController::class, 'getInProgressProjectLists']);
    Route::get('get-sale-confirm-amounts/{project_id}', [AccountantController::class, 'getSaleConfirmAmt']);
});

Route::group([
    'prefix' => 'faq',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('by-customers',[FAQItemController::class,'findCustomerQuestions']);
    Route::put('replay-customer/{id}',[FAQItemController::class,'sendReply']);
    Route::get('index', [FAQItemController::class, 'index']);
    Route::get('{id}', [FAQItemController::class, 'show']);
    Route::post('', [FAQItemController::class, 'store']);
    Route::put('{id}', [FAQItemController::class, 'update']);
    Route::delete('{id}', [FAQItemController::class, 'destroy']);
});

Route::group([
    'prefix' => 'bank-info',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index',[AccountantController::class,'getAllBankInfos']);
});

Route::group([
    'prefix' => 'qbo-expense-type',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index',[AccountantController::class,'getAllQuickBookExpenses']);
});

Route::group([
    'prefix' => 'xero',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('redirect-to-xero', [SetupController::class, 'setup']);
});
Route::get('xero/redirect-uri', [SetupController::class, 'handleCallback']);

Route::group([
    'prefix' => 'accounting-software',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('sync-data',[AccountantController::class,'syncDataWithAccountingSoftware']);
});
