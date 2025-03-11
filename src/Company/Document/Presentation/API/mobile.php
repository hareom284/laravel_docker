<?php

use Illuminate\Support\Facades\Route;
use Src\Company\Document\Presentation\API\VendorMobileController;
use Src\Company\Document\Presentation\API\TaxInvoiceMobileController;
use Src\Company\Document\Presentation\API\PurchaseOrderMobileController;
use Src\Company\Document\Presentation\API\VariationOrderMobileController;
use Src\Company\Document\Presentation\API\ProjectRequirementMobileController;
use Src\Company\Document\Presentation\API\RenovationDocumentMobileController;
use Src\Company\Document\Presentation\API\HandoverCertificateMobileController;
use Src\Company\Document\Presentation\API\QuotationTemplateItemsMobileController;

Route::group([
    'prefix' => 'quotation-template'], function () {

    //these api will use for first time on quotation create
    Route::get('get-template', [QuotationTemplateItemsMobileController::class, 'getTemplate']);

    Route::get('/allTemplates', [QuotationTemplateItemsMobileController::class, 'retrieveAllTemplate']);

});

Route::group([
    'prefix' => 'project_requirement',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index/{project_id}', [ProjectRequirementMobileController::class, 'index']);
    Route::get('{id}', [ProjectRequirementMobileController::class, 'show']);
    Route::post('', [ProjectRequirementMobileController::class, 'store']);
    Route::put('{id}', [ProjectRequirementMobileController::class, 'update']);
    Route::delete('{id}', [ProjectRequirementMobileController::class, 'destroy']);
});

Route::group([
    'prefix' => 'renovation-document',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('sign', [RenovationDocumentMobileController::class, 'sign']);
    Route::post('store', [RenovationDocumentMobileController::class, 'store']);
    Route::get('index/{project_id}/{type}', [RenovationDocumentMobileController::class, 'index']);
    Route::get('templateItemsForUpdateRenovation/{renovation_id}', [RenovationDocumentMobileController::class, 'templateForUpdate']);
    Route::get('get-signed-reno-doc/{project_id}', [RenovationDocumentMobileController::class, 'getSignedRenoDoc']);
    Route::get('get-approved-payment/{project_id}', [RenovationDocumentMobileController::class, 'getApprovedPayment']);
    Route::get('{id}/{type}', [RenovationDocumentMobileController::class, 'show']);
});


//varirantion_order apis

Route::group([
    'prefix' => 'variation_order',
    'middleware' => 'auth:sanctum'
    ],function(){
    Route::get('variation-order/get-items/{project_id}',[VariationOrderMobileController::class,'getVariationOrderItems']);
    Route::post('', [VariationOrderMobileController::class, 'store']);
});

Route::group([
    'prefix' => 'purchase-orders',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [PurchaseOrderMobileController::class, 'index']);
    Route::get('po-index/{id}', [PurchaseOrderMobileController::class, 'poListByProjectId']);
    Route::post('', [PurchaseOrderMobileController::class, 'store']);
    Route::put('update-po/{id}', [PurchaseOrderMobileController::class, 'update']);
    Route::get('{id}', [PurchaseOrderMobileController::class, 'poShow']);
    Route::get('/po/count', [PurchaseOrderMobileController::class, 'purchaseOrderCount']);
    Route::get('po-count/{projectId}', [PurchaseOrderMobileController::class, 'poCountByProjectId']);
    Route::delete('delete-items/{id}', [PurchaseOrderMobileController::class, 'destroyItem']);
    Route::put('manager-sign', [PurchaseOrderMobileController::class, 'managerSign']);
});

// statement of account
Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'tax-invoices'
], function () {
    Route::get('/{id}', [TaxInvoiceMobileController::class, "index"]);
    Route::post('saleperson-sign', [TaxInvoiceMobileController::class, "TaxInvoiceSignBySaleperson"]);
    Route::get('/show/{id}', [TaxInvoiceMobileController::class, "show"]);
    Route::get('order-by-status/lists', [TaxInvoiceMobileController::class, 'getByStatusOrder']);
    Route::post('/manager-sign',[TaxInvoiceMobileController::class, 'TaxInvoiceSignByManager']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'handover-certificates'
], function () {
    Route::get('index/{id}', [HandoverCertificateMobileController::class, 'index']);
    Route::get('/{id}', [HandoverCertificateMobileController::class, 'HandoverCertificateDetail']);
    Route::post('', [HandoverCertificateMobileController::class, 'create']);
    Route::post('customer-sign', [HandoverCertificateMobileController::class, 'customerSign']);
});

Route::group([
    'prefix' => 'vendor',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('index', [VendorMobileController::class, 'index']);
});



