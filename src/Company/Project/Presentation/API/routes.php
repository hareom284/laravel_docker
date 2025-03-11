<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Project\Presentation\API\ProjectController;
use Src\Company\Project\Presentation\API\EventController;
use Src\Company\Project\Presentation\API\ProjectQuotationController;
use Src\Company\Project\Presentation\API\ProjectReportController;
use Src\Company\Project\Presentation\API\PropertyController;
use Src\Company\Project\Presentation\API\PropertyTypeController;
use Src\Company\Project\Presentation\API\SaleReportController;
use Src\Company\Project\Presentation\API\RenovationItemScheduleController;
use Src\Company\Project\Presentation\API\ReviewController;
use Src\Company\Project\Presentation\API\SupplierCostingController;
use Src\Company\Project\Presentation\API\SupplierCostingPaymentController;
use Src\Company\Project\Presentation\API\CustomerPaymentController;
use Src\Company\Project\Presentation\API\PaymentTypeController;
use Src\Company\Project\Presentation\API\SupplierDebitController;
use Src\Company\Project\Presentation\API\TermAndConditionController;
use Src\Company\Project\Presentation\API\VendorInvoiceExpenseTypeController;

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'project'
], function () {
    Route::get('export-projects', [ProjectController::class, 'exportProjects']);
    Route::get('index', [ProjectController::class, 'index']); // saleperson Project List Card View Api
    Route::get('saleperson/tableview', [ProjectController::class, 'listView']); // saleperson Project List Table View Api
    Route::get('lists-for-others', [ProjectController::class, 'projectListsForOthers']); // Project List For Customer and other Roles 
    Route::get('lists/{customerId}', [ProjectController::class, 'customerProject']);
    Route::get('{id}', [ProjectController::class, 'show']);
    Route::post('', [ProjectController::class, 'store']);
    Route::put('{id}', [ProjectController::class, 'update']);
    Route::delete('{id}', [ProjectController::class, 'destroy']);
    Route::get('all/count', [ProjectController::class, 'countProject']);
    Route::get('for-handover/{id}', [ProjectController::class, 'projectDetailForHandover']);
    Route::get('ongoing-project/lists', [ProjectController::class, 'onGoingProjectLists']);
    Route::get('management/project-lists', [ProjectController::class, 'getProjectForManagement']);
    Route::post('cancel/{id}', [ProjectController::class, 'cancelProject']);
    Route::post('retrieve/{id}', [ProjectController::class, 'retrieveProject']);
    Route::post('toggle-freeze/{id}', [ProjectController::class, 'toggleFreezeProject']);
    // Route::post('user/send/email', [ProjectController::class, 'sendCustomerEmail']);
    Route::put('customer_payments_date/update', [ProjectController::class, 'UpdateEstimatedDate']);
    Route::get('new/project-lists', [ProjectController::class, 'getNewProjectList']);
    Route::get('cancel/get-pending-cancel-projects', [ProjectController::class, 'getPendingCancelProjects']);
    Route::post('pending-cancel/project/{id}', [ProjectController::class, 'pendingCancelProject']);
});
// Route::post('email/image-upload',[ProjectController::class, 'emailImageUpload']);
// Route::get('email/track/{secret}', [ProjectController::class, 'trackEmailBySecret']);
Route::get('projectList', [ProjectController::class, 'projectList'])->middleware('auth:sanctum');

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'property-type'
], function () {
    Route::get('index', [PropertyTypeController::class, 'index']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'suppliercosting-payment'
], function () {
    Route::get('index', [SupplierCostingPaymentController::class, 'index']);
    Route::get('{id}', [SupplierCostingPaymentController::class, 'SupplierCostingPaymentDetail']);
    Route::post('store', [SupplierCostingPaymentController::class, 'store']);
    Route::put('manager-sign', [SupplierCostingPaymentController::class, 'managerSign']);
    Route::post('download/pdf', [SupplierCostingPaymentController::class, 'downloadPdf']);
});


Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'event'
], function () {
    Route::get('index', [EventController::class, 'index']);
    Route::get('by_project/{project_id}', [EventController::class, 'eventsByProjectId']);
    Route::get('by_group', [EventController::class, 'eventsByGroup']);
    Route::get('{id}', [EventController::class, 'show']);
    Route::post('', [EventController::class, 'store']);
    Route::put('{id}', [EventController::class, 'update']);
    Route::put('change_status/{id}', [EventController::class, 'changeStatus']);
    Route::delete('{id}', [EventController::class, 'destroy']);
    Route::get('/comments/{event_id}', [EventController::class, 'eventCommentByEventId']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'sale-report'
], function () {
    Route::get('get-all-customer-payments', [SaleReportController::class, 'getAllCustomerPayment']);
    Route::get('get-all-supplier-credits', [SaleReportController::class, 'getAllSupplierCredits']);
    Route::get('get-supplier-credit-details/{id}', [SaleReportController::class, 'getSupplierCreditDetails']);
    Route::get('get-supplier-credit/{saleReportId}', [SaleReportController::class, 'getSupplierCredit']);
    Route::get('get-supplier-credit-report', [SaleReportController::class, 'getSupplierCreditReport']);
    Route::get('get-customer-payments/{saleReportId}', [SaleReportController::class, 'getCustomerPayment']);
    Route::get('get-sale-report-by-year', [SaleReportController::class, 'getSalReportByYear']);
    Route::get('get-sale-report-by-month', [SaleReportController::class, 'getSalReportByMonth']);
    Route::get('salesperson-report-by-year', [SaleReportController::class, 'getSalespersonReportByYear']);
    Route::get('salesperson-report-by-month', [SaleReportController::class, 'getSalespersonReportByMonth']);
    Route::post('store-customer-payment', [SaleReportController::class, 'storeCustomerPayment']);
    Route::post('store-customer-payment-with-qbo', [SaleReportController::class, 'storeCustomerPaymentWithQbo']);
    Route::put('refund-customer-payment/{id}', [SaleReportController::class, 'refundCustomerPayment']);
    Route::put('update-customer-payment/{id}', [SaleReportController::class, 'updateCustomerPayment']);
    Route::delete('delete-customer-payment/{id}', [SaleReportController::class, 'destroyCustomerPayment']);
    Route::post('store-supplier-credit', [SaleReportController::class, 'StoreSupplierCredit']);
    Route::put('update-supplier-credit/{id}', [SaleReportController::class, 'updateSupplierCredit']);
    Route::put('{id}', [SaleReportController::class, 'updateSaleReport']);
    Route::get('{projectId}', [SaleReportController::class, 'getSaleReportByProjectId']);
    Route::get('saleperson/kpi-report-month', [SaleReportController::class, 'salepersonKpiReportMonth']);
    Route::get('saleperson/kpi-report-year', [SaleReportController::class, 'salepersonKpiReportYear']);
    Route::get('saleperson/with-kpi-year', [SaleReportController::class, 'salepersonSaleReportWithKpiInYear']);
    Route::get('get-supplier-costing/{projectId}', [SaleReportController::class, 'getSupplierCostingsByProjectId']);
    Route::post('store-supplier-costing', [SaleReportController::class, 'storeSupplierCosting']);
    Route::post('store-supplier-costing-with-qbo', [SaleReportController::class, 'storeSupplierCostingWithQbo']);
    Route::put('update-supplier-costing/{id}', [SaleReportController::class, 'updateSupplierCosting']);
    Route::delete('delete-supplier-costing/{id}', [SaleReportController::class, 'destroySupplierCosting']);
    Route::get('company/with-kpi-year', [SaleReportController::class, 'companySaleReportWithKpiInYear']);
    Route::post('upload-job-sheet', [SaleReportController::class, 'uploadJobSheet']);
    Route::put('sign-sale-report/{id}', [SaleReportController::class, 'signSaleReport']);
});
Route::get('get-pending-documents', [SaleReportController::class, 'getPendingApprovalDocuments'])->middleware('auth:sanctum');
Route::get('get-manager-pending-documents', [SaleReportController::class, 'getManagerPendingApprovalDocuments'])->middleware('auth:sanctum');
Route::post('approve-document/{id}', [SaleReportController::class, 'approveDocument'])->middleware('auth:sanctum');
Route::post('mark-claim-commission/{id}', [SaleReportController::class, 'markClaimCommission'])->middleware('auth:sanctum');

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'advance-payment'
], function () {
    Route::get('get-all', [SaleReportController::class, 'getAllAdvancePayments']);
    Route::get('get-by-sale-report/{saleReportId}', [SaleReportController::class, 'getAdvancePaymentBySaleReportId']);
    Route::post('store', [SaleReportController::class, 'storeAdvancePayment']);
    Route::put('update/{id}', [SaleReportController::class, 'updateAdvancePayment']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'supplier-costing'
], function () {
    Route::get('index', [SupplierCostingController::class, 'index']);
    Route::get('get-report', [SupplierCostingController::class, 'getReport']);
    Route::get('get-by-vendor-and-project', [SupplierCostingController::class, 'getByVendorAndProject']);
    Route::post('{id}/approve', [SupplierCostingController::class, 'approve']);
    Route::post('{id}/verify', [SupplierCostingController::class, 'verify']);
    Route::get('{id}', [SupplierCostingController::class, 'show']);
    Route::post('import', [SupplierCostingController::class, 'import']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'supplier-debit'
], function () {
    Route::get('index', [SupplierDebitController::class, 'index']);
    Route::get('get-by-sale-report/{saleReportId}', [SupplierDebitController::class, 'getBySaleReportId']);
    Route::get('get-report', [SupplierDebitController::class, 'getReport']);
    Route::get('{id}', [SupplierDebitController::class, 'show']);
    Route::post('store', [SupplierDebitController::class, 'store']);
    Route::put('update/{id}', [SupplierDebitController::class, 'update']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'renovation_item_schedule'
], function () {
    Route::get('index/{projectId}', [RenovationItemScheduleController::class, 'index']);
    Route::get('get-evo/{projectId}', [RenovationItemScheduleController::class, 'getEvo']);
    Route::post('update-schedule', [RenovationItemScheduleController::class, 'updateSchedule']);
    Route::post('update-status/{id}', [RenovationItemScheduleController::class, 'updateStatus']);
    Route::post('update-all-status', [RenovationItemScheduleController::class, 'updateAllStatus']);
    Route::post('update-evo-status/{id}', [RenovationItemScheduleController::class, 'updateEvoItemStatus']);
    Route::post('update-all-evo-status', [RenovationItemScheduleController::class, 'updateEvoAllItemStatus']);
    Route::post('update-evo-schedule', [RenovationItemScheduleController::class, 'updateEvoSchedule']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'project-report'
], function () {
    Route::get('all/{projectId}', [ProjectReportController::class, 'getProjectReport']);
});

Route::group([
    'prefix' => 'review',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [ReviewController::class, 'index']);
    Route::get('{id}', [ReviewController::class, 'show']);
    Route::post('', [ReviewController::class, 'store']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'customer-payment'
], function () {
    Route::get('export-invoice', [CustomerPaymentController::class, 'exportInvoice']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'projects'
], function () {
    Route::get('/{projectId}/export', [ProjectController::class, 'exportProfitAndLoss']);
});

Route::group([
    'prefix' => 'payment-type',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [PaymentTypeController::class, 'index']);
    Route::get('{id}', [PaymentTypeController::class, 'show']);
    Route::post('', [PaymentTypeController::class, 'store']);
    Route::put('{id}', [PaymentTypeController::class, 'update']);
    Route::delete('{id}', [PaymentTypeController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'vendor-invoice-expense-type'
], function () {
    Route::get('index', [VendorInvoiceExpenseTypeController::class, 'index']);
    Route::get('all', [VendorInvoiceExpenseTypeController::class, 'all']);
    Route::get('{id}', [VendorInvoiceExpenseTypeController::class, 'show']);
    Route::post('', [VendorInvoiceExpenseTypeController::class, 'store']);
    Route::put('{id}', [VendorInvoiceExpenseTypeController::class, 'update']);
    Route::delete('{id}', [VendorInvoiceExpenseTypeController::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'term-and-conditions'
], function () {
    Route::get('index', [TermAndConditionController::class, 'index']);
    Route::get('{id}', [TermAndConditionController::class, 'show']);
    Route::post('', [TermAndConditionController::class, 'store']);
    Route::put('{id}', [TermAndConditionController::class, 'update']);
    Route::delete('{id}', [TermAndConditionController::class, 'destroy']);
    Route::get('index/all', [TermAndConditionController::class, 'getAll']);
    Route::post('download/sample-pdf/{id}', [TermAndConditionController::class, 'downloadPdf']);
    Route::post('download/signed-pdf/{id}', [TermAndConditionController::class, 'downloadSignedPdf']);
    Route::post('download/unsigned-pdf/{id}', [TermAndConditionController::class, 'downloadUnSignPdf']);
    Route::put('customer/sign', [TermAndConditionController::class, 'customerSign']);
});