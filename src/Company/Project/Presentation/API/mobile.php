<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Project\Presentation\API\ProjectMobileController;
use Src\Company\Project\Presentation\API\PropertyTypeMobileController;
use Src\Company\Project\Presentation\API\RenovationItemScheduleMobileController;
use Src\Company\Project\Presentation\API\SaleReportMobileController;
use Src\Company\Project\Presentation\API\StripeMobileController;
use Src\Company\Project\Presentation\API\SupplierCostingMobileController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'project'
], function () {
    Route::get('index', [ProjectMobileController::class, 'index']);
    Route::post('store', [ProjectMobileController::class, 'store']);
    Route::get('{id}', [ProjectMobileController::class, 'show']);
    Route::put('update/{id}', [ProjectMobileController::class, 'update']);
    Route::get('for-handover/{id}', [ProjectMobileController::class, 'projectDetailForHandover']);
    Route::get('management/project-lists', [ProjectMobileController::class, 'getProjectForManagement']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'property-type'
], function () {
    Route::get('index', [PropertyTypeMobileController::class, 'index']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'sales-report'
], function () {
    Route::get('{salespersonUserId}/kpi-report-month', [SaleReportMobileController::class, 'salepersonKpiReportMonth']);
    Route::get('get-customer-payments/{saleReportId}', [SaleReportMobileController::class, 'getCustomerPayment']);
    Route::get('get-supplier-costing/{projectId}', [SaleReportMobileController::class, 'getSupplierCostingsByProjectId']);
    Route::get('get-supplier-credit/{saleReportId}', [SaleReportMobileController::class, 'getSupplierCredit']);
    Route::put('{id}', [SaleReportMobileController::class, 'updateSaleReport']);
    Route::get('{projectId}', [SaleReportMobileController::class, 'getSaleReportByProjectId']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'project-report'
], function () {
    Route::get('all/{projectId}', [ProjectMobileController::class, 'getProjectReport']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'supplier-costing'
], function () {
    Route::get('index',[SupplierCostingMobileController::class,'index']);
    Route::get('{id}', [SupplierCostingMobileController::class, 'show']);
    Route::post('{id}/verify', [SupplierCostingMobileController::class, 'verify']);
    Route::post('{id}/approve', [SupplierCostingMobileController::class, 'approve']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'renovation_item_schedule'
], function () {
    Route::get('index/{projectId}', [RenovationItemScheduleMobileController::class, 'index']);
    Route::get('section-by-date/{projectId}', [RenovationItemScheduleMobileController::class, 'getSectionsByDate']);
    Route::post('update-schedule', [RenovationItemScheduleMobileController::class, 'updateSchedule']);
    Route::post('update-status/{id}', [RenovationItemScheduleMobileController::class, 'updateStatus']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'renovation_section'
], function () {
    Route::post('upload', [RenovationItemScheduleMobileController::class, 'uploadImages']);
    Route::post('delete', [RenovationItemScheduleMobileController::class, 'deleteImage']);
    Route::get('get-section-images', [RenovationItemScheduleMobileController::class, 'getSectionImages']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'payment'
], function () {
    Route::post('make-payment', [StripeMobileController::class, 'createPaymentIntent']);
});
