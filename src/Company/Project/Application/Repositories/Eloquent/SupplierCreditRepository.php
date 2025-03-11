<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Application\Mappers\SupplierCreditMapper;
use Src\Company\Project\Domain\Model\Entities\SupplierCredit;
use Src\Company\Project\Domain\Repositories\SupplierCreditRepositoryInterface;
use Src\Company\Project\Domain\Resources\SupplierCreditResource;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class SupplierCreditRepository implements SupplierCreditRepositoryInterface
{
    private $accountingService;
    
    public function __construct(AccountingServiceInterface $accountingService = null)
    {
        $this->accountingService = $accountingService;
    }

    public function index(array $filters): array
    {
        $perPage = $filters['perPage'] ?? 10;

        $query = SupplierCreditEloquentModel::query();

        if(isset($filters['vendorId'])){

            $vendorId = $filters['vendorId'];

            $query->where('vendor_id',$vendorId);
        }

        $supplierCreditsEloquent = $query->orderBy('id', 'DESC')->paginate($perPage);

        $supplierCredits =  SupplierCreditResource::collection($supplierCreditsEloquent);

        $links = [
            'first' => $supplierCredits->url(1),
            'last' => $supplierCredits->url($supplierCredits->lastPage()),
            'prev' => $supplierCredits->previousPageUrl(),
            'next' => $supplierCredits->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $supplierCredits->currentPage(),
            'from' => $supplierCredits->firstItem(),
            'last_page' => $supplierCredits->lastPage(),
            'path' => $supplierCredits->url($supplierCredits->currentPage()),
            'per_page' => $perPage,
            'to' => $supplierCredits->lastItem(),
            'total' => $supplierCredits->total(),
        ];

        $responseData['data'] = $supplierCredits;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getById($id): SupplierCreditResource
    {
        $supplierCredit = SupplierCreditEloquentModel::query()->findOrFail($id);

        return new SupplierCreditResource($supplierCredit);
    }

    public function getBySaleReportId($saleReportId)
    {
        $supplierCredits = SupplierCreditEloquentModel::where('sale_report_id', $saleReportId)->get();

        $data['results'] = SupplierCreditResource::collection($supplierCredits);

        $data['sum_amount'] = number_format($supplierCredits->sum('amount'), 2, '.', ',');
        $data['total_amount'] = number_format($supplierCredits->sum('total_amount'), 2, '.', ',');
        $data['total_gst_amount'] = number_format($supplierCredits->sum('gst_amount'), 2, '.', ',');

        return $data;
    }

    public function getReport(array $filters)
    {
        $year = $filters['year'];

        $query = SupplierCreditEloquentModel::query()->whereYear('invoice_date', $year);
        $debitQuery = SupplierDebitEloquentModel::query()->whereYear('invoice_date', $year);

        if (is_null($filters['vendorId'])) {
            // If month is 0, do not filter by month
            if ($filters['month'] == 0) {

                $result = $query->get();

                $debitResult = $debitQuery->get();

            } else {
                // Filter by the specified month
                $result = $query->whereMonth('invoice_date', $filters['month'])->get();

                $debitResult = $debitQuery->whereMonth('invoice_date', $filters['month'])->get();
            }
        } else {
            // Add vendorId filter to the query
            $query->where('vendor_id', $filters['vendorId']);

            $debitQuery->where('vendor_id', $filters['vendorId']);

            // If month is 0, do not filter by month
            if ($filters['month'] == 0) {
                $result = $query->get();

                $debitResult = $debitQuery->get();

            } else {
                // Filter by the specified month
                $result = $query->whereMonth('invoice_date', $filters['month'])->get();

                $debitResult = $debitQuery->whereMonth('invoice_date', $filters['month'])->get();
            }
        }

        $grandTotal = 0;
        $debitGrandTotal = $debitResult->sum('total_amount');

        $finalResult = SupplierCreditResource::collection($result)->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('M');
        })->map(function ($groupedItems) use (&$grandTotal) {

            $total = $groupedItems->reduce(function ($carry, $item) {
                return $carry + $item->total_amount;
            }, 0);

            $grandTotal += $total;

            return [
                'total' => $total,
                'data' => $groupedItems,
            ];
        });

        return [
            'data' => $finalResult,
            'grand_total' => $grandTotal,
            'debit_grand_total' => $debitGrandTotal,
            'balance_credit' => $grandTotal - $debitGrandTotal,
        ];
    }

    public function store(SupplierCredit $supplierCredit): SupplierCreditData
    {
        $storeSupplierCredit = SupplierCreditMapper::toEloquent($supplierCredit);

        $storeSupplierCredit->save();

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            $existsQboVendorId = $storeSupplierCredit->vendor->quick_book_vendor_id;
            $companyId = $storeSupplierCredit->saleReport->project->company_id;

            if(is_null($existsQboVendorId)){

                $vendorName = $storeSupplierCredit->vendor->vendor_name;

                $quickBookVendor = $this->accountingService->getVendorByName($companyId,$vendorName);

                if(!$quickBookVendor){

                    $vendorData = [
                        'name' => $storeSupplierCredit->vendor->vendor_name,
                        'contact_person' => $storeSupplierCredit->vendor->contact_person,
                        'contact_no' => $storeSupplierCredit->vendor->contact_person_number,
                        'email' => $storeSupplierCredit->vendor->email,
                        'street_name' => $storeSupplierCredit->vendor->street_name,
                        'postal_code' => $storeSupplierCredit->vendor->postal_code,
                    ];

                    $qboRecentVendor = $this->accountingService->storeVendor($companyId,$vendorData);

                    $qboVendorId = $qboRecentVendor->Id;

                    VendorEloquentModel::find($storeSupplierCredit->vendor->id)->update([
                        'quick_book_vendor_id' => $qboVendorId
                    ]);

                }else{

                    if($generalSettingEloquent->value == 'quickbook'){
                        $qboVendorId = $quickBookVendor->Id;
                        
                        VendorEloquentModel::find($storeSupplierCredit->vendor->id)->update([
                            'quick_book_vendor_id' => $qboVendorId
                        ]);

                    }else{
                        $qboVendorId = $quickBookVendor;

                        VendorEloquentModel::find($storeSupplierCredit->vendor->id)->update([
                            'xero_vendor_id' => $qboVendorId
                        ]);
                    }
                }

            }else{
                $qboVendorId = $existsQboVendorId;
            }
            
            try {
                
                if($generalSettingEloquent->value == 'quickbook'){
                    $taxRateRef = $storeSupplierCredit->is_gst_inclusive == 1 ? 63 : 18;
                    $taxPercent = $storeSupplierCredit->is_gst_inclusive == 1 ? 9 : 0;
                    $taxCodeRef = $storeSupplierCredit->is_gst_inclusive == 1 ? 58 : 21;
                    $globalTaxCalculation = $storeSupplierCredit->is_gst_inclusive == 1 ? 'TaxExcluded' : 'TaxInclusive';

                }else{

                    $taxCodeRef = $storeSupplierCredit->is_gst_inclusive == 1 ? 'INPUTY24' : 'NONE';
                    $taxPercent = null; //did not use in xero integration
                    $taxRateRef = null; //did not use in xero integration;
                    $globalTaxCalculation = $storeSupplierCredit->is_gst_inclusive == 1 ? 'Exclusive' : 'NoTax';
                }

                $supplierCreditData = [
                    'txnDate' => $storeSupplierCredit->invoice_date,
                    'invoiceNo' => $storeSupplierCredit->invoice_no,
                    'description' => $storeSupplierCredit->description,
                    'amount' => $storeSupplierCredit->amount,
                    'gstValue' => $storeSupplierCredit->gst_amount,
                    'totalAmount' => $storeSupplierCredit->total_amount,
                    'taxRateRef' => $taxRateRef,
                    'taxCodeRef' => $taxCodeRef,
                    'taxPercent' => $taxPercent,
                    'vendorID' => $qboVendorId,
                    'globalTaxCalculation' => $globalTaxCalculation,
                    'currencyCode' => 'SGD',
                    'accountCode' => 445,
                ];

                $quickBookVendorCredit = $this->accountingService->storeVendorCredit($companyId,$supplierCreditData);

                if($generalSettingEloquent->value == 'quickbook'){
                    SupplierCreditEloquentModel::find($storeSupplierCredit->id)->update([
                        'quick_book_vendor_credit_id' => $quickBookVendorCredit->Id,
                    ]);
                }else{
                    SupplierCreditEloquentModel::find($storeSupplierCredit->id)->update([
                        'xero_credit_note_id' => $quickBookVendorCredit->getCreditNoteId(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::debug($e);
            }
        }

        return SupplierCreditMapper::fromEloquent($storeSupplierCredit);
    }

    public function update(SupplierCredit $supplierCredit): SupplierCreditEloquentModel
    {
        $supplierCreditEloquent = SupplierCreditEloquentModel::query()->findOrFail($supplierCredit->id);

        $supplierCreditEloquent->invoice_no = $supplierCredit->invoice_no;

        $supplierCreditEloquent->description = $supplierCredit->description;

        $supplierCreditEloquent->is_gst_inclusive = $supplierCredit->is_gst_inclusive;

        $supplierCreditEloquent->total_amount = $supplierCredit->total_amount;

        $supplierCreditEloquent->amount = $supplierCredit->amount;

        $supplierCreditEloquent->gst_amount = $supplierCredit->gst_amount;

        $supplierCreditEloquent->invoice_date = $supplierCredit->invoice_date;

        $supplierCreditEloquent->vendor_id = $supplierCredit->vendor_id;

        $supplierCreditEloquent->invoice_date = $supplierCredit->invoice_date;

        if($supplierCredit->pdf_path != null)
        {
            $supplierCreditEloquent->pdf_path = $supplierCredit->pdf_path;
        }

        $supplierCreditEloquent->save();

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            $existsQboVendorId = $supplierCreditEloquent->vendor->quick_book_vendor_id;

            $companyId = $supplierCreditEloquent->saleReport->project->company_id;

            if(is_null($existsQboVendorId)){

                $vendorName = $supplierCreditEloquent->vendor->vendor_name;

                $quickBookVendor = $this->accountingService->getVendorByName($companyId,$vendorName);

                if(!$quickBookVendor){

                    $vendorData = [
                        'name' => $supplierCreditEloquent->vendor->vendor_name,
                        'contact_person' => $supplierCreditEloquent->vendor->contact_person,
                        'contact_no' => $supplierCreditEloquent->vendor->contact_person_number,
                        'email' => $supplierCreditEloquent->vendor->email,
                        'street_name' => $supplierCreditEloquent->vendor->street_name,
                        'postal_code' => $supplierCreditEloquent->vendor->postal_code,
                    ];

                    $qboRecentVendor = $this->accountingService->storeVendor($companyId,$vendorData);

                    $qboVendorId = $qboRecentVendor->Id;

                    VendorEloquentModel::find($supplierCreditEloquent->vendor->id)->update([
                        'quick_book_vendor_id' => $qboVendorId
                    ]);

                }else{

                    $qboVendorId = $quickBookVendor->Id;

                    VendorEloquentModel::find($supplierCreditEloquent->vendor->id)->update([
                        'quick_book_vendor_id' => $qboVendorId
                    ]);
                }

            }else{
                $qboVendorId = $existsQboVendorId;
            }
            
            try {

                $qboSupplierCreditId = $supplierCreditEloquent->quick_book_vendor_credit_id;
                $taxRateRef = $supplierCreditEloquent->is_gst_inclusive == 1 ? 63 : 18;
                $taxPercent = $supplierCreditEloquent->is_gst_inclusive == 1 ? 9 : 0;
                $taxCodeRef = $supplierCreditEloquent->is_gst_inclusive == 1 ? 58 : 21;
                $globalTaxCalculation = $supplierCreditEloquent->is_gst_inclusive == 1 ? 'TaxExcluded' : 'TaxInclusive';

                $supplierCreditData = [
                    'txnDate' => $supplierCreditEloquent->invoice_date,
                    "invoiceNo" => $supplierCreditEloquent->invoice_no,
                    'description' => $supplierCreditEloquent->description,
                    'amount' => $supplierCreditEloquent->amount,
                    'gstValue' => $supplierCreditEloquent->gst_amount,
                    'totalAmount' => $supplierCreditEloquent->total_amount,
                    'taxRateRef' => $taxRateRef,
                    'taxCodeRef' => $taxCodeRef,
                    'taxPercent' => $taxPercent,
                    'vendorID' => $qboVendorId,
                    'globalTaxCalculation' => $globalTaxCalculation
                ];

                $this->accountingService->updateVendorCredit($companyId,$qboSupplierCreditId,$supplierCreditData);
                
            } catch (\Exception $e) {
                
                Log::debug($e);
            }
        }

        return $supplierCreditEloquent;
    }
}