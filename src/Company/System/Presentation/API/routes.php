<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Document\Presentation\API\ContractController;
use Src\Company\System\Presentation\API\AccountingSettingController;
use Src\Company\System\Presentation\API\UserController;
use Src\Company\System\Presentation\API\CompanyController;
use Src\Company\System\Presentation\API\CompanyKpiController;
use Src\Company\System\Presentation\API\RankController;
use Src\Company\System\Presentation\API\SalepersonMonthlyKpiController;
use Src\Company\System\Presentation\API\SalepersonYearlyKpiController;
use Src\Company\System\Presentation\API\SiteSettingController;
use Src\Company\System\Presentation\API\GeneralSettingController;


Route::get('email/track/{secret}', [UserController::class, 'trackEmailBySecret']);
Route::post('pdf-test', [ContractController::class, 'pdfTest']);


Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'user'
], function () {
    Route::get('salesperson', [UserController::class, 'getSalepersonList']);
    Route::get('saleperson-lists', [UserController::class, 'getSalesPerson']);
    Route::get('vendor/designer-lists', [UserController::class, 'getDesignerListsForVendorFilter']);
    Route::get('lead', [UserController::class, 'getCustomerList']);
    Route::get('drafter', [UserController::class, 'getDrafterList']);
    Route::get('rankList', [UserController::class, 'getRankList']);
    Route::put('assign-rank/{saleperson_id}/{rank_id}', [UserController::class, 'assignSalepersonRank']);
    Route::post('send/email', [UserController::class, 'sendCustomerEmail']);
    Route::post('email/image-upload', [UserController::class, 'emailImageUpload']);
    Route::get('management-or-manager-list', [UserController::class, 'getManagementOrManagerList']);
    Route::get('saleperson/sale-report-list', [UserController::class, 'getSalepersonReportList']);
    Route::post('excel-import-lead',[UserController::class, 'customerExcelImport']);
    Route::post('excel-import-staffs',[UserController::class, 'staffExcelImport']);
    // Route::post('create-user', [UserController::class, 'createUser']);
    // Route::put('update-user/{id}', [UserController::class, 'updateUser']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'company-kpi'
], function () {
    Route::get('', [CompanyKpiController::class, 'index']);
    Route::get('with-year', [CompanyKpiController::class, 'kpiRecordByYear']);
    Route::post('', [CompanyKpiController::class, 'store']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'company'
], function () {
    Route::get('index', [CompanyController::class, 'index']);
    Route::get('all', [CompanyController::class, 'all']);
    Route::get('default-one', [CompanyController::class, 'getDefaultCompany']);
    Route::get('{id}', [CompanyController::class, 'show']);
    Route::post('', [CompanyController::class, 'store']);
    Route::put('{id}', [CompanyController::class, 'update']);
    Route::put('update-default-company/{id}', [CompanyController::class, 'updateDefaultCompany']);
    Route::put('update-accounting-software-company-ids/{companies}', [CompanyController::class, 'updateAccountingSoftwareCompanyIds']);
    Route::delete('{id}', [CompanyController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'site_setting'
], function () {
    Route::put('{id}', [SiteSettingController::class, 'update']);
    Route::get('{id}', [SiteSettingController::class, 'show']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'general_setting'
], function () {
    Route::put('', [GeneralSettingController::class, 'update']);
    Route::get('all', [GeneralSettingController::class, 'showAll']);
    Route::get('{generalSetting}', [GeneralSettingController::class, 'show']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'accounting_setting'
], function () {
    Route::get('get-by-company/{companyId}', [AccountingSettingController::class, 'show']);
    Route::put('update-settings', [AccountingSettingController::class, 'update']);
});

Route::get('logo', [SiteSettingController::class, 'getLogoAndFavicon']);
