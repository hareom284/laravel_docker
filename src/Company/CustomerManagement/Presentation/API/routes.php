<?php

use Illuminate\Support\Facades\Route;
use Src\Company\CustomerManagement\Presentation\API\CheckListItemsController;
use Src\Company\CustomerManagement\Presentation\API\CustomerController;
use Src\Company\CustomerManagement\Presentation\API\IdMilestoneController;
use Src\Company\CustomerManagement\Presentation\API\ReferrerFormController;
use Src\Company\CustomerManagement\Presentation\API\RejectedReasonController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'customer'
], function () {
    Route::post('', [CustomerController::class, 'createLead']);
    Route::put('{id}', [CustomerController::class, 'updateLead']);
    Route::get('lead-for-manager', [CustomerController::class, 'customerListForManager']);
    Route::put('inactive/{id}', [CustomerController::class, 'inactiveCustomer']);
    Route::put('active/{id}', [CustomerController::class, 'activeCustomer']);
    Route::get('/detail/{id}', [CustomerController::class, 'customerDetail']);
    Route::get('customers-with-email', [CustomerController::class, 'customersWithEmail']);
    Route::get('/user/lead', [CustomerController::class, 'getCustomerList']);

    Route::get('lead', [CustomerController::class, 'customerList']);
    Route::get('get-customer-lists', [CustomerController::class, 'getCustomers']);
    Route::post('lead-management-report', [CustomerController::class, 'salepersonLeadManagementReport']);
    Route::get('lead-management-list', [CustomerController::class, 'salepersonLeadManagementList']);
    Route::get('group-lead-management-list', [CustomerController::class, 'groupSalepersonLeadManagementList']);
    Route::get('manager-lead-management-list', [CustomerController::class, 'managerLeadManagementList']);
    Route::post('id-milestone-update', [CustomerController::class, 'changeIdMilestones']);
    Route::post('checklist-status/update', [CustomerController::class, 'updateCheckListStatus']);
    Route::get('get/campaigns', [CustomerController::class, 'getCampaignList']);

    Route::post('excel-import-lead',[CustomerController::class, 'customerExcelImport']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'id_milestone'
], function () {
    Route::get('index', [IdMilestoneController::class, 'index']);
    Route::get('action-lists',[IdMilestoneController::class, 'idMilestoneActions']);
    Route::get('show/{id}', [IdMilestoneController::class, 'show']);
    Route::post('', [IdMilestoneController::class, 'store']);
    Route::put('{id}', [IdMilestoneController::class, 'update']);
    Route::put('/order/update', [IdMilestoneController::class, 'orderUpdate']);
    Route::delete('{id}', [IdMilestoneController::class, 'destroy']);
    Route::get('whatsapp_templates', [IdMilestoneController::class, 'getWhatsappTemplates']);

});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'rejected_reason'
], function () {
    Route::get('index', [RejectedReasonController::class, 'index']);
    Route::get('show/{id}', [RejectedReasonController::class, 'show']);
    Route::post('', [RejectedReasonController::class, 'store']);
    Route::put('{id}', [RejectedReasonController::class, 'update']);
    Route::put('/order/update', [RejectedReasonController::class, 'orderUpdate']);
    Route::delete('{id}', [RejectedReasonController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'checklist-items'
], function () {
    Route::get('{id}', [CheckListItemsController::class, 'checkListByCustomerId']);
    Route::post('', [CheckListItemsController::class, 'create']);
    Route::delete('{id}', [CheckListItemsController::class, 'destroy']);
    Route::put('complete-check/{id}', [CheckListItemsController::class, 'completeCheck']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'referrer-forms'
],function(){
    Route::get('index', [ReferrerFormController::class, 'index']);
    Route::post('', [ReferrerFormController::class, 'store']);
    Route::get('{id}', [ReferrerFormController::class, 'show']);
    Route::put('sign/{id}',[ReferrerFormController::class, 'sign']);
    Route::post('download-pdf/{id}',[ReferrerFormController::class,'downloadPDF']);
    Route::get('get/approved-referrers',[ReferrerFormController::class, 'getApprovedReferrer']);
});