<?php

use Illuminate\Support\Facades\Route;
use Src\Company\UserManagement\Presentation\API\UserController;
use Src\Company\UserManagement\Presentation\API\PermissionController;
use Src\Company\UserManagement\Presentation\API\RoleController;
use Src\Company\UserManagement\Presentation\API\TeamController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'user'
], function () {
    Route::get('index', [UserController::class, 'index']);
    Route::get('userShow/{id}', [UserController::class, 'show']);
    Route::post('', [UserController::class, 'createStaffUser']); // use for creating user with staff role
    Route::put('{id}', [UserController::class, 'updateStaffUser']); // use for updating user with staff role
    Route::post('create-user', [UserController::class, 'createCustomerUser']); // use for creating user with customer role
    Route::post('create-vendor-user', [UserController::class, 'createVendorUser']); // use for creating user with vendor role
    Route::put('update-user/{id}', [UserController::class, 'updateCustomerUser']); // use for updating user with customer role
    Route::put('update-profile/{id}', [UserController::class, 'updateProfile']);
    Route::delete('{id}', [UserController::class, 'destroy']);
    Route::get('users-for-selectbox', [UserController::class, 'selectBoxUsers']);
    Route::get('managers',[UserController::class,'getManagers']);
    Route::get('team-members',[UserController::class,'getTeamMembers']);
    Route::post('update-import', [UserController::class, 'userExcelUpdateImport']);
    Route::get('get-survey/{id}', [UserController::class, 'getSurveyByUserId']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'permission'
], function () {
    Route::get('index', [PermissionController::class, 'index']);
    Route::get('list', [PermissionController::class, 'permissionList']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'role'
], function () {
    Route::get('index', [RoleController::class, 'index']);
    Route::get('{id}', [RoleController::class, 'show']);
    Route::post('', [RoleController::class, 'store']);
    Route::put('{id}', [RoleController::class, 'update']);
    Route::delete('{id}', [RoleController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'team'
], function () {
    Route::get('index', [TeamController::class, 'index']);
    Route::get('{id}', [TeamController::class, 'show']);
    Route::post('', [TeamController::class, 'store']);
    Route::put('{id}', [TeamController::class, 'update']);
    Route::delete('{id}', [TeamController::class, 'destroy']);
});


