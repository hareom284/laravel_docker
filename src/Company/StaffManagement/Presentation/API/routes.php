<?php

use Illuminate\Support\Facades\Route;
use Src\Company\StaffManagement\Presentation\API\RankController;
use Src\Company\StaffManagement\Presentation\API\SalepersonMonthlyKpiController;
use Src\Company\StaffManagement\Presentation\API\SalepersonYearlyKpiController;
use Src\Company\StaffManagement\Presentation\API\StaffController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'staff'
], function () {
    Route::post('', [StaffController::class, 'store']);
    Route::put('{id}', [StaffController::class, 'update']);

    Route::post('excel-import-staffs',[StaffController::class, 'staffExcelImport']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'rank'
], function () {
    Route::get('', [RankController::class, 'index']);
    Route::post('', [RankController::class, 'store']);
    Route::put('/update', [RankController::class, 'update']);
    Route::delete('/{id}', [RankController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'saleperson-yearly-kpi'
], function () {
    Route::get('', [SalepersonYearlyKpiController::class, 'index']);
    Route::get('with-year', [SalepersonYearlyKpiController::class, 'kpiRecordByYear']);
    Route::post('', [SalepersonYearlyKpiController::class, 'store']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'saleperson-monthly-kpi'
], function () {
    Route::get('', [SalepersonMonthlyKpiController::class, 'index']);
    Route::get('with-month', [SalepersonMonthlyKpiController::class, 'kpiRecordByMonth']);
    Route::post('', [SalepersonMonthlyKpiController::class, 'store']);
});
