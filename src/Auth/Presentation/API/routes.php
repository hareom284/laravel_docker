<?php

use Illuminate\Support\Facades\Route;
use Src\Auth\Presentation\API\AuthController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use  Illuminate\Console\Command;
use Aimeos\Upscheme\Task\MShopAddDataAbstract;
use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;

/**
 * Authentication Routes
 *
 *  23.5.23
 */



Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::post('recover_password', [AuthController::class, 'recoverPassword']);

    Route::post('reset_password', [AuthController::class, 'resetPassword']);

    Route::post('check_token', [AuthController::class, 'checkToken']);

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::post('verify_email', [AuthController::class, 'verifyEmail'])->middleware('auth:sanctum');

    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    Route::get('get_permissions', [AuthController::class, 'getPermissions'])->middleware('auth:sanctum');

    Route::post('update_password', [AuthController::class, 'updatePassword'])->middleware('auth:sanctum');
});


Route::get('check_php_version', function () {
    return response()->json([
        'php_version' => phpversion()
    ]);
});

Route::get('get-state-map', function(Request $request){
$customer = CustomerEloquentModel::find($request->customer_id);
return $customer->getStateMap();
});
Route::post('test-state-map', function (Request $request) {
$customer = CustomerEloquentModel::find($request->customer_id);
return $customer->transition($request->action);
});
