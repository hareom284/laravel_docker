<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Document\Presentation\API\EvoController;
use Src\Company\Document\Presentation\API\FOCController;
use Src\Company\Document\Presentation\API\FolderController;
// use Src\Company\Document\Presentation\API\SectionController;
// use Src\Company\Document\Presentation\API\AreaOfWorkController;
use Src\Company\Document\Presentation\API\VendorController;
use Src\Company\Document\Presentation\API\ContractController;
use Src\Company\Document\Presentation\API\DocumentController;
use Src\Company\Document\Presentation\API\HDBFormsController;
use Src\Company\Document\Presentation\API\MaterialController;
use Src\Company\Document\Presentation\API\DesignWorkController;
use Src\Company\Document\Presentation\API\TaxInvoiceController;
use Src\Company\Document\Presentation\API\EvoTemplateController;
use Src\Company\Document\Presentation\API\MeasurementController;
use Src\Company\Document\Presentation\API\PaymentTermController;
use Src\Company\Document\Presentation\API\CancellationController;
use Src\Company\Document\Presentation\API\ThreeDDesignController;
use Src\Company\Document\Presentation\API\WorkScheduleController;
use Src\Company\Document\Presentation\API\PurchaseOrderController;
use Src\Company\Document\Presentation\API\VariationOrderController;
use Src\Company\Document\Presentation\API\VendorCategoryController;
use Src\Company\Document\Presentation\API\ElectricalPlansController;
use Src\Company\Document\Presentation\API\DocumentStandardController;
use Src\Company\Document\Presentation\API\ProjectPortfolioController;
use Src\Company\Document\Presentation\API\ProjectRequirementController;
use Src\Company\Document\Presentation\API\RenovationDocumentController;
use Src\Company\Document\Presentation\API\HandoverCertificateController;
use Src\Company\Document\Presentation\API\QuotationTemplateItemsController;
use Src\Company\Document\Presentation\API\PurchaseOrderTemplateItemContoller;
use Src\Company\Document\Presentation\API\QuotationTemplateCategoryController;
use Src\Company\Document\Presentation\API\SupplierInvoiceController;

Route::group([
    'prefix' => 'folder',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [FolderController::class, 'index']);
    Route::get('/by-project/{projectId}', [FolderController::class, 'folderByProjectId']);
    Route::get('{id}', [FolderController::class, 'show']);
    Route::post('', [FolderController::class, 'store']);
    Route::put('{id}', [FolderController::class, 'update']);
    Route::delete('{id}', [FolderController::class, 'destroy']);
});

Route::group([
    'prefix' => 'document',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [DocumentController::class, 'index']);
    Route::get('/by-project/{projectId}', [DocumentController::class, 'documentListByProjectId']);
    Route::get('{id}', [DocumentController::class, 'show']);
    Route::post('', [DocumentController::class, 'store']);
    Route::put('{id}', [DocumentController::class, 'update']);
    Route::delete('{id}', [DocumentController::class, 'destroy']);
});

Route::group([
    'prefix' => 'vendor',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [VendorController::class, 'index']);
    Route::get('get-by-user/{userId}', [VendorController::class, 'getVendorByUserId']);
    Route::get('{id}', [VendorController::class, 'show']);
    Route::post('', [VendorController::class, 'store']);
    Route::put('{id}', [VendorController::class, 'update']);
    Route::delete('{id}', [VendorController::class, 'destroy']);
    Route::post('template-import',[VendorController::class, 'vendorExcelImport']);
    Route::post('update-import', [VendorController::class, 'vendorExcelUpdateImport']);
});

Route::group([
    'prefix' => 'vendor-category',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [VendorCategoryController::class, 'index']);
    Route::post('', [VendorCategoryController::class, 'store']);
    Route::put('{id}', [VendorCategoryController::class, 'update']);
    Route::delete('{id}', [VendorCategoryController::class, 'destroy']);
});

Route::group([
    'prefix' => 'project_requirement',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [ProjectRequirementController::class, 'index']);
    Route::get('{id}', [ProjectRequirementController::class, 'show']);
    Route::post('', [ProjectRequirementController::class, 'store']);
    Route::put('{id}', [ProjectRequirementController::class, 'update']);
    Route::delete('{id}', [ProjectRequirementController::class, 'destroy']);
});

// Route::group([
//     'prefix' => 'section',
//     'middleware' => 'auth:sanctum'
// ], function () {
//     // Route::get('index', [SectionController::class, 'index']);
//     Route::get('{id}', [SectionController::class, 'show']);
//     Route::post('', [SectionController::class, 'store']);
//     Route::put('{id}', [SectionController::class, 'update']);
//     Route::delete('{id}', [SectionController::class, 'destroy']);
// });

// Route::group([
//     'prefix' => 'area_of_work',
//     'middleware' => 'auth:sanctum'
// ], function () {
//     Route::get('index', [AreaOfWorkController::class, 'index']);
//     Route::post('', [AreaOfWorkController::class, 'store']);
//     Route::delete('{id}', [AreaOfWorkController::class, 'destroy']);
// });

Route::group([
    'prefix' => 'document_standard',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [DocumentStandardController::class, 'index']);
    Route::get('{id}', [DocumentStandardController::class, 'show']);
    Route::post('', [DocumentStandardController::class, 'store']);
    Route::put('{id}', [DocumentStandardController::class, 'update']);
    Route::delete('{id}', [DocumentStandardController::class, 'destroy']);
    Route::get('company/{id}', [DocumentStandardController::class, 'findDataByCompanyId']);
});

Route::group([
    'prefix' => 'contract',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [ContractController::class, 'index']);
    Route::post('', [ContractController::class, 'store']);
    Route::put('/sign', [ContractController::class, 'signContract']);
    Route::put('/customer-sign', [ContractController::class, 'customerSign']);
    Route::get('{contract_id}', [ContractController::class, 'contractDetail']);
    Route::post('download/pdf', [ContractController::class, 'downloadPdf']);
    Route::post('download/pdf/contract', [ContractController::class, 'downloadContractPdf']);
});

Route::group([
    'prefix' => 'renovation_document',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('agreement-no', [RenovationDocumentController::class, 'generateRenovationDocumentAgrNo']);
    Route::get('pending-reno', [RenovationDocumentController::class, 'getPendingRenoDoc']);
    Route::post('upload-singed-document/{renovation_document}/{document_type}',[RenovationDocumentController::class,'uploadSignedDocument']);
});

Route::group([
    'prefix' => 'quotation',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('sign', [RenovationDocumentController::class, 'sign']);
    Route::get('templateItemsForUpdateRenovation/{renovation_id}', [RenovationDocumentController::class, 'templateForUpdate']);
    Route::get('show/template/{document_id}', [RenovationDocumentController::class, 'showTemplate']);
    Route::get('index/{project_id}/{type}', [RenovationDocumentController::class, 'index']);
    Route::get('{id}/{type}', [RenovationDocumentController::class, 'show']);
    Route::post('sendMail', [RenovationDocumentController::class, 'sendMail']);
    Route::post('', [RenovationDocumentController::class, 'store']);
    Route::delete('{id}/{project_id}', [RenovationDocumentController::class, 'remove']);
    Route::post('download/pdf', [RenovationDocumentController::class, 'downloadPdf']);
    Route::post('{renovation_document}/{status}', [RenovationDocumentController::class, 'updateDocumentStatus']);
    Route::put('update/quotation-detail/{document_id}', [RenovationDocumentController::class, 'updateQuotationDetail']);
});

Route::group([
    'prefix' => 'cancellation',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('count/{project_id}', [CancellationController::class, 'countLists']);
    Route::get('getItems/{project_id}', [CancellationController::class, 'getAllCancellationItems']);
    Route::post('', [CancellationController::class, 'store']);
});

Route::group([
    'prefix' => 'variation-order',
    'middleware' => 'auth:sanctum'
], function () {

    Route::get('getItems/{project_id}', [VariationOrderController::class, 'getAllVariationOrderItems']);
    Route::get('/update-getItems/{document_id}', [VariationOrderController::class, 'getUpdateAllVariationOrderItems']);
    Route::get('index/{project_id}', [VariationOrderController::class, 'index']);
    Route::get('count/{project_id}', [VariationOrderController::class, 'countLists']);
    Route::post('', [VariationOrderController::class, 'store']);
});

Route::group([
    'prefix' => 'foc',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('getItems/{project_id}', [FOCController::class, 'getAllFOCItems']);
    Route::get('count/{project_id}', [FOCController::class, 'countLists']);
    Route::post('', [FOCController::class, 'store']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'milestones_and_timelines'
], function () {
    Route::get('getRenovationItems/{projectId}', [RenovationDocumentController::class, 'getRenovationItemsWithSections']);
});


Route::group([
    'prefix' => 'quotation_template_items',
], function () {
    Route::get('index', [QuotationTemplateItemsController::class, 'index']);
    Route::post('/store', [QuotationTemplateItemsController::class, 'templateStore']);
    Route::get('/allTemplates', [QuotationTemplateItemsController::class, 'retrieveAllTemplate']);
    Route::get('/template', [QuotationTemplateItemsController::class, 'retrieveTemplate']);
    Route::post('/saleperson-store', [QuotationTemplateItemsController::class, 'salepersonTemplateStore']);
    Route::post('/duplicateTemplate', [QuotationTemplateItemsController::class, 'duplicateTemplate']);
    Route::post('', [QuotationTemplateItemsController::class, 'store']);
    Route::put('{id}', [QuotationTemplateItemsController::class, 'update']);
    Route::delete('{id}', [QuotationTemplateItemsController::class, 'destroy']);
    Route::post('/import-excel', [QuotationTemplateItemsController::class, 'excelImport']);
    Route::post('/createTemplate', [QuotationTemplateItemsController::class, 'createTemplate']);
    Route::post('/updateTemplate', [QuotationTemplateItemsController::class, 'updateTemplate']);
    Route::delete('/deleteTemplate/{id}', [QuotationTemplateItemsController::class, 'deleteTemplate']);// delete quotationtemplate

    Route::get('/suggestion_are_of_works', [QuotationTemplateItemsController::class, 'suggestionAreaOfWork']);
});

Route::group([
    'prefix' => 'design_work',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [DesignWorkController::class, 'index']);
    Route::post('sign', [DesignWorkController::class, 'sign']);
    Route::get('{id}', [DesignWorkController::class, 'show']);
    Route::post('', [DesignWorkController::class, 'store']);
    Route::put('{id}', [DesignWorkController::class, 'update']);
    Route::delete('{id}', [DesignWorkController::class, 'destroy']);
});

Route::group([
    'prefix' => '3d_design',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [ThreeDDesignController::class, 'getByProjectId']);
    Route::get('{id}', [ThreeDDesignController::class, 'show']);
    Route::post('', [ThreeDDesignController::class, 'store']);
    Route::put('{id}', [ThreeDDesignController::class, 'update']);
    Route::delete('{id}', [ThreeDDesignController::class, 'delete']);
});

Route::group([
    'prefix' => 'work_schedule',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/{id}', [WorkScheduleController::class, 'getWorkSchedules']);
    Route::get('/detail/{id}', [WorkScheduleController::class, 'show']);
    Route::post('', [WorkScheduleController::class, 'store']);
    Route::delete('{id}', [WorkScheduleController::class, 'delete']);
});

Route::group([
    'prefix' => 'supplier_invoice',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('/{id}', [SupplierInvoiceController::class, 'getSupplierInvoices']);
    Route::get('/detail/{id}', [SupplierInvoiceController::class, 'show']);
    Route::post('', [SupplierInvoiceController::class, 'store']);
    Route::delete('{id}', [SupplierInvoiceController::class, 'delete']);
});

Route::group([
    'prefix' => 'material',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [MaterialController::class, 'index']);
});

Route::group([
    'prefix' => 'hdb-forms',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [HDBFormsController::class, 'index']);
    Route::get('{id}', [HDBFormsController::class, 'show']);
    Route::post('', [HDBFormsController::class, 'store']);
    Route::post('{id}', [HDBFormsController::class, 'update']);
    Route::delete('{id}', [HDBFormsController::class, 'destroy']);
});

Route::group([
    'prefix' => 'electrical-plans',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [ElectricalPlansController::class, 'index']);
    // Route::get('{id}', [ElectricalPlansController::class, 'show']);
    Route::post('', [ElectricalPlansController::class, 'store']);
    // Route::post('{id}', [ElectricalPlansController::class, 'update']);
    Route::delete('{id}', [ElectricalPlansController::class, 'destroy']);
});

Route::group([
    'prefix' => 'evo-template',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('store-item', [EvoTemplateController::class, 'storeItem']);
    Route::post('store-room', [EvoTemplateController::class, 'storeRoom']);
    Route::put('update-item/{id}', [EvoTemplateController::class, 'updateItem']);
    Route::put('update-room/{id}', [EvoTemplateController::class, 'updateRoom']);
    Route::delete('item/{id}', [EvoTemplateController::class, 'destroyItem']);
    Route::delete('room/{id}', [EvoTemplateController::class, 'destroyRoom']);
    Route::get('item-index', [EvoTemplateController::class, 'templateItemList']);
    Route::get('room-index', [EvoTemplateController::class, 'templateRoomList']);
    Route::get('', [EvoTemplateController::class, 'index']);
});

Route::group([
    'prefix' => 'purchase-orders',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [PurchaseOrderController::class, 'index']);
    Route::get('item-index/{id}', [PurchaseOrderController::class, 'poItemList']);
    Route::get('get-company-stamp/{projectId}', [PurchaseOrderController::class, 'getCompanyStamp']);
    Route::get('count', [PurchaseOrderController::class, 'purchaseOrderCount']);
    Route::get('po-index/{projectId}', [PurchaseOrderController::class, 'poListByProjectId']);
    Route::get('po-count/{projectId}', [PurchaseOrderController::class, 'poCountByProjectId']);
    Route::get('{id}', [PurchaseOrderController::class, 'poShow']);
    Route::post('', [PurchaseOrderController::class, 'store']);
    Route::post('{id}', [PurchaseOrderController::class, 'updateForSaleReport']);
    Route::put('update-po/{id}', [PurchaseOrderController::class, 'update']);
    Route::delete('{id}', [PurchaseOrderController::class, 'destroy']);
    Route::delete('item/{id}', [PurchaseOrderController::class, 'destroyItem']);
    Route::get('get-by-status/{status}', [PurchaseOrderController::class, 'purchaseOrderByStatus']);
    Route::get('po/lists', [PurchaseOrderController::class, 'purchaseOrderList']);
    Route::put('manager-sign', [PurchaseOrderController::class, 'managerSign']);
    Route::post('download/pdf', [PurchaseOrderController::class, 'downloadPdf']);
    Route::get('get-quotation-item/{projectId}', [PurchaseOrderController::class, 'getQuotationItemForPO']);
});

Route::group([
    'prefix' => 'purchase-orders-template-items',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [PurchaseOrderTemplateItemContoller::class, 'index']);
    Route::get('for-po-create/{companyId}/{vendorCategoryId}', [PurchaseOrderTemplateItemContoller::class, 'getItmesForPoCreate']);
    Route::post('', [PurchaseOrderTemplateItemContoller::class, 'store']);
    Route::put('{id}', [PurchaseOrderTemplateItemContoller::class, 'update']);
    Route::delete('{id}', [PurchaseOrderTemplateItemContoller::class, 'destroy']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'handover-certificates'
], function () {
    Route::get('', [HandoverCertificateController::class, 'HandoverCertificateLists']);
    Route::get('approve-handover-certificate-lists', [HandoverCertificateController::class, 'approveRequireHandoverCertificates']);
    Route::put('manager-sign', [HandoverCertificateController::class, 'managerSign']);
    Route::post('customer-sign', [HandoverCertificateController::class, 'customerSign']);
    Route::post('handover-sign', [HandoverCertificateController::class, 'handoverSign']);
    Route::get('/{id}', [HandoverCertificateController::class, 'HandoverCertificateDetail']);
    Route::get('/with-project/{project_id}', [HandoverCertificateController::class, 'HandoverCertificateListsByProjectId']);
    Route::post('', [HandoverCertificateController::class, 'create']);
    Route::post('download/pdf', [HandoverCertificateController::class, 'handoverdownloadPdf']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'tax-invoices'
], function () {
    Route::post('saleperson-sign', [TaxInvoiceController::class, "TaxInvoiceSignBySaleperson"]);
    Route::post('manager-sign', [TaxInvoiceController::class, "TaxInvoiceSignByManager"]);
    Route::get('/{id}', [TaxInvoiceController::class, "index"]);
    Route::get('/show/{id}', [TaxInvoiceController::class, "show"]);
    Route::get('order-by-status/lists', [TaxInvoiceController::class, 'getByStatusOrder']);
    Route::post('download/pdf', [TaxInvoiceController::class, 'taxInvoicedownloadPdf']);
    Route::put('change/status/{id}', [TaxInvoiceController::class, 'changeTaxInvoiceStatus']);
});

Route::group([
    'prefix' => 'evo',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('', [EvoController::class, 'store']);
    Route::get('index/{project_id}', [EvoController::class, 'getEvoByProjectId']);
    Route::get('{id}', [EvoController::class, 'show']);
    Route::delete('{id}', [EvoController::class, 'destroy']);
    Route::post('sign', [EvoController::class, 'sign']);
    Route::get('count/{project_id}', [EvoController::class, 'countLists']);
    Route::get('get-document-standard/{project_id}', [EvoController::class, 'getDocumentStandard']);
    Route::post('download/pdf', [EvoController::class, 'downloadPdf']);
    Route::get('edit/{id}', [EvoController::class, 'edit']);
});

Route::group([
    'prefix' => 'measurement',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('', [MeasurementController::class, 'store']);
    Route::get('index', [MeasurementController::class, 'index']);
});

Route::group([
    'prefix' => 'payment-term',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [PaymentTermController::class, 'index']);
    Route::get('{id}', [PaymentTermController::class, 'show']);
    Route::post('', [PaymentTermController::class, 'store']);
    Route::put('{id}', [PaymentTermController::class, 'update']);
    Route::delete('{id}', [PaymentTermController::class, 'destroy']);
    Route::put('send-request/{id}',[PaymentTermController::class, 'sendRequest']);
    Route::post('approve-request/{id}',[PaymentTermController::class, 'approveRequest']);
});



Route::group([
    'prefix' => 'project-portfolio',
    'middleware' => 'auth:sanctum'
],function(){
    Route::get('project-protflio-sale-person/{sale_person_id}',[ProjectPortfolioController::class,'getProjectBySalePerson']);
    Route::get('index/{project_id}', [ProjectPortfolioController::class, 'getByProjectId']);
    Route::get('{project_portfolios}', [ProjectPortfolioController::class, 'show']);
    Route::post('', [ProjectPortfolioController::class, 'store']);
    Route::put('{project_portfolios}', [ProjectPortfolioController::class, 'update']);
    Route::delete('{project_portfolios}', [ProjectPortfolioController::class, 'delete']);

});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'quotation-template-category'
], function () {
    Route::get('index', [QuotationTemplateCategoryController::class, 'index']);
    Route::get('show/{id}', [QuotationTemplateCategoryController::class, 'show']);
    Route::post('', [QuotationTemplateCategoryController::class, 'store']);
    Route::put('{id}', [QuotationTemplateCategoryController::class, 'update']);
    Route::delete('{id}', [QuotationTemplateCategoryController::class, 'destroy']);
    Route::put('move/{id}', [QuotationTemplateCategoryController::class, 'moveTemplate']);
    Route::get('index/get-salesperson-quotation-template-categories/{id}', [QuotationTemplateCategoryController::class, 'getSalespersonQuotationTemplateCategory']);

});
