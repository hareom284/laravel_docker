<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Src\Company\Project\Application\DTO\SupplierCostingData;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Project\Domain\Resources\SupplierCostingResource;
use Src\Company\Project\Application\Mappers\SupplierCostingMapper;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Domain\Mail\NotifyExceedSupplierCostingAmountMail;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QboExpenseTypeEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingApprovalEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\AccountingSettingEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;

class SupplierCostingRepository implements SupplierCostingRepositoryInterface
{
    private $quickBookService;
    private $accountingService;

    public function __construct(QuickbookService $quickBookService, AccountingServiceInterface $accountingService = null)
    {
        $this->quickBookService = $quickBookService;
        $this->accountingService = $accountingService;
    }

    public function index(array $filters)
    {
        $perPage = $filters['perPage'] ?? 10;

        $query = SupplierCostingEloquentModel::where('project_id', '!=', 0);

        if(isset($filters['vendorId'])){

            $vendorId = $filters['vendorId'];

            $query->where('vendor_id',$vendorId);
        }

        if(isset($filters['projectId'])){

            $projectId = $filters['projectId'];

            $query->where('project_id',$projectId);
        }

        if (isset($filters['designerId'])) {

            $designerId = $filters['designerId'];

            // Filter the SupplierCostingEloquentModel based on designerId
            $query->whereHas('project', function ($query) use ($designerId) {
                $query->whereHas('salespersons', function ($query) use ($designerId) {
                    $query->where('salesperson_id', $designerId);
                });
            });
        }

        if(isset($filters['status'])){

            $status = $filters['status'];

            $query->where('status',$status);
        }

        $supplierCostingsEloquent = $query->orderBy('status','ASC')->orderBy('invoice_date','DESC')->paginate($perPage);

        $supplierCostings =  SupplierCostingResource::collection($supplierCostingsEloquent);

        $links = [
            'first' => $supplierCostings->url(1),
            'last' => $supplierCostings->url($supplierCostings->lastPage()),
            'prev' => $supplierCostings->previousPageUrl(),
            'next' => $supplierCostings->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $supplierCostings->currentPage(),
            'from' => $supplierCostings->firstItem(),
            'last_page' => $supplierCostings->lastPage(),
            'path' => $supplierCostings->url($supplierCostings->currentPage()),
            'per_page' => $perPage,
            'to' => $supplierCostings->lastItem(),
            'total' => $supplierCostings->total(),
        ];

        $responseData['data'] = $supplierCostings;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getByProjectId($projectId)
    {
        $supplierCostings = SupplierCostingEloquentModel::where('project_id', $projectId)
            ->where(function ($query) {
                // Retrieve where there is a PO relationship and PO status is 3
                $query->whereHas('purchaseOrder', function ($query) {
                    $query->where('status', 3);
                });

                // OR retrieve where there is no PO relationship
                $query->orWhereDoesntHave('purchaseOrder');
            })->get();

        $data['data'] = SupplierCostingResource::collection($supplierCostings);

        return $data;
    }

    public function getByVendorAndProject($vendorId,$projectId)
    {
        $supplierCostings = SupplierCostingEloquentModel::where('project_id', $projectId)->where('vendor_id', $vendorId)->get();

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        $companyId = ProjectEloquentModel::find($projectId)->company_id;

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            $accountingSoftwareStatus = true;
        }else{
                
            $accountingSoftwareStatus = false;
        }

        $data['data'] = SupplierCostingResource::collection($supplierCostings);
        $data['is_qbo_integration'] = $accountingSoftwareStatus;
        $data['company_id'] = $companyId;

        return $data;
    }

    public function getById($id)
    {
        $qboConfig = config('quickbooks');

        $supplierCosting = SupplierCostingEloquentModel::findOrFail($id);

        $finalResult = new SupplierCostingResource($supplierCosting);

        $data['data'] = $finalResult;
        $data['is_qbo_integration'] = $qboConfig['qbo_integration'];

        return $data;
    }

    public function getReport(array $filters)
    {
        $year = $filters['year'];

        $query = SupplierCostingEloquentModel::query()->where('status',3)->whereYear('invoice_date',$year);

        if (is_null($filters['vendorId'])) {
            // If month is 0, do not filter by month
            if ($filters['month'] == 0) {

                $result = $query->get();

            } else {
                // Filter by the specified month
                $result = $query->whereMonth('created_at', $filters['month'])->get();
            }
        } else {
            // Add vendorId filter to the query
            $query->where('vendor_id', $filters['vendorId']);

            // If month is 0, do not filter by month
            if ($filters['month'] == 0) {
                $result = $query->get();

            } else {
                // Filter by the specified month
                $result = $query->whereMonth('created_at', $filters['month'])->get();
            }
        }

        $grandTotal = 0;

        $finalResult = SupplierCostingResource::collection($result)->groupBy(function ($item) {
            return Carbon::parse($item->invoice_date)->format('M');
        })->map(function ($groupedItems) use (&$grandTotal) {

            $total = $groupedItems->reduce(function ($carry, $item) {
                return $carry + ($item->payment_amt - $item->discount_amt);
            }, 0);

            $grandTotal += $total;

            return [
                'total' => $total,
                'data' => $groupedItems,
            ];
        });

        return [
            'data' => $finalResult,
            'grand_total' => $grandTotal
        ];
    }

    public function store(SupplierCosting $supplierCosting): SupplierCostingData
    {
        $supplierCosting = SupplierCostingMapper::toEloquent($supplierCosting);

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting','enable_approive_supplier_costing')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value == 'true'){
            $supplierCosting->status = 2;
        }

        if(is_null($supplierCosting->project_id))
        {
            $supplierCosting->status = 2;
            
        }elseif($supplierCosting->project->project_status == 'Completed')
        {
            $supplierCosting->status = 1;
        }

        $supplierCosting->save();

        if(!is_null($supplierCosting->project_id))
        {
            $totalAmount =SupplierCostingEloquentModel::where('project_id',$supplierCosting->project_id)->sum('payment_amt');

            $projectRevenue = SaleReportEloquentModel::where('project_id',$supplierCosting->project_id)->first();

            if($totalAmount > $projectRevenue->total_sales)
            {
                $projectAddress = $supplierCosting->project->property->block_num . ' ' . $supplierCosting->project->property->street_name . ' ' . $supplierCosting->project->property->unit_num;
                $salePersons = $supplierCosting->project->salespersons->map(function ($salesperson) {
                    return $salesperson->first_name . ' ' . $salesperson->last_name;
                })->implode("', '");
                
                // Wrap the string with single quotes
                $salePersons = "('" . $salePersons . "')";
    
                $exceedingAmount = $totalAmount - $projectRevenue->total_sales;
                $revenueAmount = $projectRevenue->total_sales;
    
                $notifyExceedProjectRevenueInSupplierCostingMail = new NotifyExceedSupplierCostingAmountMail($projectAddress,$salePersons,$revenueAmount,$totalAmount,$exceedingAmount);
    
                $notifyUsers = UserEloquentModel::whereHas('roles', function ($query) {
                    $query->where('role_id', 2)
                        ->orWhere('role_id', 4);
                })->get();
    
                foreach ($notifyUsers as $user) {
    
                    $email = $user->email;
    
                    Mail::to($email)->send($notifyExceedProjectRevenueInSupplierCostingMail);
                }
            }
        }

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none' && !is_null($supplierCosting->quick_book_expense_id)){

            if($generalSettingEloquent->value == 'quickbooks'){
                $existsQboCustomerId = $supplierCosting->project->customer->quick_book_user_id;
            }else{
                $existsQboCustomerId = $supplierCosting->project->customer->xero_user_id;
            }

            if(is_null($existsQboCustomerId)){

                $customerFirstName = $supplierCosting->project->customer->first_name;
                $customerLastName = $supplierCosting->project->customer->last_name;

                if(is_null($customerLastName) || $customerLastName == ''){
                    $customerName = $customerFirstName;
                }else{
                    $customerName = $customerFirstName . ' ' . $customerLastName;
                }

                $type = $supplierCosting->project->customer->customers->customer_type ? 1 : 0;

                $quickBookCustomer = $this->accountingService->getCustomer($supplierCosting->project->company_id,$customerName);

                if(!$quickBookCustomer){

                    $customerEmail = $supplierCosting->project->customer->email;
                    $customerNo = $supplierCosting->project->customer->contact_no;
                    $address = $supplierCosting->project->property->block_num . ' ' . $supplierCosting->project->property->street_name . ' ' . $supplierCosting->project->property->unit_num;
                    $postalCode = $supplierCosting->project->property->postal_code;

                    $customerData = [
                        'name' => $customerName,
                        'companyName' => ($type === 1) ? $customerName : null,
                        'email' => $customerEmail,
                        'address' => $address,
                        'postal_code' => $postalCode,
                        'contact_no' => $customerNo
                    ];

                    $qboRecentCusomterId = $this->accountingService->storeCustomer($supplierCosting->project->company_id,$customerData);

                    $qboCustomerId = $qboRecentCusomterId;

                    UserEloquentModel::find($supplierCosting->project->customer->id)->update([
                        'quick_book_user_id' => $qboCustomerId
                    ]);

                }else{

                    $qboCustomerId = $quickBookCustomer->Id;

                    UserEloquentModel::find($supplierCosting->project->customer->id)->update([
                        'quick_book_user_id' => $qboCustomerId
                    ]);
                }

            }else{

                $qboCustomerId = $existsQboCustomerId;
            }

            $vendorName = $supplierCosting->vendor->vendor_name;

            $quickBookVendor = $this->accountingService->getVendorByName($supplierCosting->project->company_id,$vendorName);

            if(!$quickBookVendor){

                $vendorData = [
                    'name' => $supplierCosting->vendor->vendor_name,
                    'contact_person' => $supplierCosting->vendor->contact_person,
                    'contact_no' => $supplierCosting->vendor->contact_person_number,
                    'email' => $supplierCosting->vendor->email,
                    'street_name' => $supplierCosting->vendor->street_name,
                    'postal_code' => $supplierCosting->vendor->postal_code,
                ];

                $qboRecentVendorId = $this->accountingService->storeVendor($supplierCosting->project->company_id,$vendorData);

                if($generalSettingEloquent->value == 'quickbooks'){
                    $qboVendorId = $qboRecentVendorId->Id;
                }else{
                    $qboVendorId = $qboRecentVendorId;
                }

            }else{
                if($generalSettingEloquent->value == 'quickbooks'){
                    $qboVendorId = $quickBookVendor->Id;
                }else{
                    $qboVendorId = $quickBookVendor;
                }
            }
            try {

                $isTaxCalculationNeeded = is_null($supplierCosting->project->company->gst_reg_no) ? false : true ;

                $isGstEnable = $supplierCosting->is_gst_inclusive == 1 ? true : false;

                if($generalSettingEloquent->value == 'quickbooks'){

                    $accountingSettings = AccountingSettingEloquentModel::where('company_id',$supplierCosting->project->company_id)->get();

                    $taxCodeRef = optional($accountingSettings->where('setting', 'taxCode')->first())->value;
                    $taxRateRef = optional($accountingSettings->where('setting', 'taxRate')->first())->value;
                    $rebateCategoryRef = optional($accountingSettings->where('setting', 'rebateCategory')->first())->value;
                    $billTaxCalculation = optional($accountingSettings->where('setting', 'billTaxCalculation')->first())->value;

                    $taxPercent = $supplierCosting->is_gst_inclusive == 1 ? 9 : 0;
                    $globalTaxCalculation = $supplierCosting->is_gst_inclusive == 1 ? $billTaxCalculation : 'NotApplicable';

                }else{

                    $taxCodeRef = $supplierCosting->is_gst_inclusive == 1 ? 'OUTPUTY24' : 'NONE';
                    $taxPercent = null; //did not use in xero integration
                    $taxRateRef = null; //did not use in xero integration;
                    $rebateCategoryRef = null;
                    $globalTaxCalculation = $supplierCosting->is_gst_inclusive == 1 ? 'Exclusive' : 'NoTax';
                }
                
                $totalAmountOfBill = $supplierCosting->payment_amt;
                
                $expenseType = QboExpenseTypeEloquentModel::find($supplierCosting->quick_book_expense_id);

                if($generalSettingEloquent->value == 'quickbooks'){
                    $quickBookExpenseID = $expenseType->quick_book_id;
                }else{
                    $quickBookExpenseID = $expenseType->xero_id;
                }

                $isRebateAmountSetting = GeneralSettingEloquentModel::where('setting','enable_rebate_amount_as_item_in_costing')->first();

                if($isRebateAmountSetting && $isRebateAmountSetting->value == 'true' && $supplierCosting->discount_amt > 0){

                    $isRebateAmountEnable = true;
                }else{

                    $isRebateAmountEnable = false;
                }

                if($isGstEnable){

                    if($generalSettingEloquent->value == 'quickbooks'){

                        if($globalTaxCalculation === 'TaxInclusive' ){

                            if($isRebateAmountEnable){

                                $discountPercentage = $supplierCosting->discount_percentage;

                                $firstLineItemAmount = $totalAmountOfBill - $supplierCosting->gst_value;

                                $secondLineItemAmount = $firstLineItemAmount * ($discountPercentage / 100);

                                $netAmountTaxable = $firstLineItemAmount - $secondLineItemAmount;

                                $gstAmount = $netAmountTaxable * 0.09;

                                $finalAmount = $netAmountTaxable + $gstAmount;

                            }else{

                                $firstLineItemAmount = $totalAmountOfBill - $supplierCosting->gst_value;

                                $secondLineItemAmount = 0;

                                $netAmountTaxable = $firstLineItemAmount;

                                $gstAmount = $netAmountTaxable * 0.09;

                                $finalAmount = $netAmountTaxable + $gstAmount;
                            }

                        }else{

                            $discountPercentage = $supplierCosting->discount_percentage;

                            if($discountPercentage > 0){

                                $firstLineItemAmount = $totalAmountOfBill;

                                $secondLineItemAmount = $supplierCosting->discount_amt;

                                $gstAmount = $supplierCosting->gst_value;

                                $finalAmount = $totalAmountOfBill - $supplierCosting->discount_amt;
                            }else{

                                $firstLineItemAmount = $totalAmountOfBill;
                            
                                $secondLineItemAmount = 0;
    
                                $gstAmount = $supplierCosting->gst_value;

                                $finalAmount = $firstLineItemAmount;
                            }

                            $netAmountTaxable = $firstLineItemAmount;
                        }

                    }else{

                        $discountPercentage = $supplierCosting->discount_percentage;

                        if($discountPercentage > 0){

                            $firstLineItemAmount = $totalAmountOfBill;

                            $secondLineItemAmount = $supplierCosting->discount_amt;

                            $gstAmount = $supplierCosting->gst_value;

                            $finalAmount = $totalAmountOfBill - $supplierCosting->discount_amt;
                        }else{

                            $firstLineItemAmount = $totalAmountOfBill;
                        
                            $secondLineItemAmount = 0;

                            $gstAmount = $supplierCosting->gst_value;

                            $finalAmount = $firstLineItemAmount;
                        }

                        $netAmountTaxable = $firstLineItemAmount;
                    }

                }else{

                    $discountPercentage = $supplierCosting->discount_percentage;

                    if($discountPercentage > 0){

                        $firstLineItemAmount = $totalAmountOfBill;

                        $secondLineItemAmount = $supplierCosting->discount_amt;

                        $finalAmount = $totalAmountOfBill - $supplierCosting->discount_amt;

                    }else{

                        $firstLineItemAmount = $totalAmountOfBill;
                    
                        $secondLineItemAmount = 0;

                        $finalAmount = $totalAmountOfBill;
                    }

                    $gstAmount = 0;

                    $netAmountTaxable = $firstLineItemAmount;                    
                }

                $billData = [
                    'vendorID' => $qboVendorId,
                    'userID' => $qboCustomerId,
                    'totalAmount' => $finalAmount,
                    'firtLineItemAmount' => $firstLineItemAmount,
                    'secondLineItemAmount' =>  $isRebateAmountEnable ? -abs($secondLineItemAmount) : 0,
                    'netAmountTaxable' => $netAmountTaxable,
                    'totalTax' => $gstAmount,
                    'isRebateAmountEnable' => $isRebateAmountEnable,
                    'invoiceDate' => $supplierCosting->invoice_date,
                    'description' => $supplierCosting->description,
                    'invoiceNo' => $supplierCosting->invoice_no,
                    'PrivateNote' => $supplierCosting->description,
                    'taxCodeRef' => $taxCodeRef,
                    'taxRateRef' => $taxRateRef,
                    'taxPercent' => $taxPercent,
                    'rebateCategoryRef' => $rebateCategoryRef,
                    'globalTaxCalculation' => $globalTaxCalculation,
                    'quickBookExpenseID' => $quickBookExpenseID,
                    'classRef' =>  $supplierCosting->project->quickbook_class_id,
                    'isGstEnable' => $isGstEnable,
                    'isTaxCalculationNeeded' => $isTaxCalculationNeeded
                ];

                Log::info('Bill Data:', $billData);

                $quickBookBillId = $this->accountingService->storeBill($supplierCosting->project->company_id,$billData);

                if($generalSettingEloquent->value == 'quickbooks'){

                    SupplierCostingEloquentModel::find($supplierCosting->id)->update([
                        'quick_book_bill_id' => $quickBookBillId,
                    ]);

                }else{

                    SupplierCostingEloquentModel::find($supplierCosting->id)->update([
                        'xero_bill_id' => $quickBookBillId->getInvoiceId(),
                    ]);
                }

            } catch (\Exception $e) {

                Log::debug($e);
            }
        }

        return SupplierCostingMapper::fromEloquent($supplierCosting);
    }

    public function importFromQbo($projectId)
    {
        $project = ProjectEloquentModel::find($projectId);

        if(is_null($project->quickbook_class_id)){

            $classFromQbo = $this->accountingService->getProjectByName($project->company_id, $project->agreement_no);

            $qboClassId = is_null($classFromQbo) ? null : $classFromQbo->Id;
        }else{

            $qboClassId = $project->quickbook_class_id;
        }

        if(!is_null($qboClassId)){

            $filterRemark = $qboClassId . '-' . $project->company_id;

            $supplierCostings = SupplierCostingEloquentModel::where('remark', $filterRemark)
                                                                ->where('project_id', 0)
                                                                ->get()
                                                                ->groupBy('vendor_id');

            foreach ($supplierCostings as $vendorId => $costings) {

                $vendorFromQbo = $this->accountingService->getVendorById($project->company_id, $vendorId);

                $vendor = VendorEloquentModel::where('vendor_name', $vendorFromQbo->DisplayName)->first();

                foreach ($costings as $costing) {
                    $costing->vendor_id = $vendor->id;
                    $costing->project_id = $project->id;

                    $costing->save();
                }
            }
        }

        return true;
    }

    public function storeWithQbo($companyId)
    {
        $vendorInvoicesFromQBo = $this->accountingService->getBillByCompanyId($companyId);

        if(is_null($vendorInvoicesFromQBo)){
            throw new Exception("This Project does not have a vendor invoices");
        }else{

            foreach($vendorInvoicesFromQBo as $invoice)
            {
                if (isset($invoice->Line)) {

                    $lines = is_array($invoice->Line) ? $invoice->Line : [$invoice->Line];

                    // Get the first line's ClassRef
                    $paymentAmount = $lines[0]->Amount;
                    $invoiceClass = $lines[0]->AccountBasedExpenseLineDetail->ClassRef ?? null;
                
                    if (count($lines) > 1) {
                        $discountAmount = $lines[1]->Amount < 0 ? abs($lines[1]->Amount) : 0;
                    } else {
                        $discountAmount = 0;
                    }

                    if($discountAmount > 0){
                        $discountPercentage = ($discountAmount / $paymentAmount) * 100;
                    }else{
                        $discountPercentage = 0;
                    }
                
                    if (!is_null($invoice->TxnTaxDetail)) {
                        $gstStatus = (float)$invoice->TxnTaxDetail->TotalTax > 0;
                        
                    }else{
                        $gstStatus = false;
                    }

                    if(!is_null($invoiceClass)){
                        SupplierCostingEloquentModel::create([
                            'invoice_no' => $invoice->DocNumber,
                            'payment_amt' => $paymentAmount,
                            'invoice_date' => $invoice->TxnDate,
                            'description' => "Supplier Invoice From QBO",
                            'remark' => $invoiceClass . '-' . $companyId,
                            'is_gst_inclusive' => $gstStatus,
                            'discount_percentage' => $discountPercentage,
                            'discount_amt' => $discountAmount,
                            'gst_value' => $gstStatus ? $invoice->TxnTaxDetail->TotalTax : 0,
                            'project_id' => 0,
                            'status' => 1,
                            'vendor_id' => $invoice->VendorRef,
                            'quick_book_bill_id' => $invoice->Id
                        ]);
                    }else{

                        continue;
                    }
                } else {
                    
                    continue; // Skip this invoice
                }
            }
    
            return true;   
        }

        return $vendorInvoicesFromQBo;
    }

    public function update(SupplierCosting $supplierCosting): SupplierCostingEloquentModel
    {

        $supplierCostingEloquent = SupplierCostingEloquentModel::query()->findOrFail($supplierCosting->id);

        $supplierCostingEloquent->invoice_no = $supplierCosting->invoice_no;

        $supplierCostingEloquent->description = $supplierCosting->description;

        $supplierCostingEloquent->payment_amt = $supplierCosting->payment_amt;

        $supplierCostingEloquent->amended_amt = $supplierCosting->amended_amt;

        $supplierCostingEloquent->remark = $supplierCosting->remark ?? '';

        $supplierCostingEloquent->amount_paid = $supplierCosting->amount_paid;

        $supplierCostingEloquent->to_pay = $supplierCosting->to_pay;

        $supplierCostingEloquent->discount_percentage = $supplierCosting->discount_percentage;

        $supplierCostingEloquent->discount_amt = $supplierCosting->discount_amt;

        $supplierCostingEloquent->credit_amt = $supplierCosting->credit_amt;

        $supplierCostingEloquent->project_id = $supplierCosting->project_id;

        $supplierCostingEloquent->vendor_id = $supplierCosting->vendor_id;

        $supplierCostingEloquent->quick_book_expense_id = $supplierCosting->quick_book_expense_id;

        $supplierCostingEloquent->is_gst_inclusive = $supplierCosting->is_gst_inclusive;

        $supplierCostingEloquent->gst_value = $supplierCosting->gst_value;

        $supplierCostingEloquent->invoice_date = $supplierCosting->invoice_date;

        // if($supplierCosting->document_file != null)
        // {
            $supplierCostingEloquent->document_file = $supplierCosting->document_file;
        // }

        $supplierCostingEloquent->save();

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value == 'quickbooks')
        {
            $vendorName = $supplierCostingEloquent->vendor->vendor_name;

            $quickBookVendor = $this->accountingService->getVendorByName($supplierCostingEloquent->project->company_id,$vendorName);

            if(!$quickBookVendor){

                $vendorData = [
                    'name' => $supplierCostingEloquent->vendor->vendor_name,
                    'contact_person' => $supplierCostingEloquent->vendor->contact_person,
                    'contact_no' => $supplierCostingEloquent->vendor->contact_person_number,
                    'email' => $supplierCostingEloquent->vendor->email,
                    'street_name' => $supplierCostingEloquent->vendor->street_name,
                    'postal_code' => $supplierCostingEloquent->vendor->postal_code,
                ];

                $qboRecentVendorId = $this->accountingService->storeVendor($supplierCostingEloquent->project->company_id,$vendorData);

                if($generalSettingEloquent->value == 'quickbooks'){
                    $qboVendorId = $qboRecentVendorId->Id;
                }else{
                    $qboVendorId = $qboRecentVendorId;
                }

            }else{

                if($generalSettingEloquent->value == 'quickbooks'){
                    $qboVendorId = $quickBookVendor->Id;
                }else{
                    $qboVendorId = $quickBookVendor;
                }
            }
            
            try {

                $isTaxCalculationNeeded = is_null($supplierCostingEloquent->project->company->gst_reg_no) ? false : true ;

                $isGstEnable = $supplierCosting->is_gst_inclusive == 1 ? true : false;

                if($generalSettingEloquent->value == 'quickbooks'){

                    $accountingSettings = AccountingSettingEloquentModel::where('company_id',$supplierCostingEloquent->project->company_id)->get();

                    $taxCodeRef = optional($accountingSettings->where('setting', 'taxCode')->first())->value;
                    $taxRateRef = optional($accountingSettings->where('setting', 'taxRate')->first())->value;
                    $rebateCategoryRef = optional($accountingSettings->where('setting', 'rebateCategory')->first())->value;
                    $billTaxCalculation = optional($accountingSettings->where('setting', 'billTaxCalculation')->first())->value;
                    
                    $taxPercent = $supplierCosting->is_gst_inclusive == 1 ? 9 : 0;
                    $globalTaxCalculation = $supplierCosting->is_gst_inclusive == 1 ? $billTaxCalculation : 'NotApplicable';

                    $qboCustomerId = $supplierCostingEloquent->project->customer->quick_book_user_id;

                }else{

                    $taxCodeRef = $supplierCosting->is_gst_inclusive == 1 ? 'OUTPUTY24' : 'NONE';
                    $taxPercent = null; //did not use in xero integration
                    $taxRateRef = null; //did not use in xero integration;
                    $rebateCategoryRef = null; //did not use in xero integration;
                    $globalTaxCalculation = $supplierCosting->is_gst_inclusive == 1 ? 'Exclusive' : 'NoTax';
                    
                    $qboCustomerId = $supplierCostingEloquent->project->customer->xero_user_id;
                }

                $totalAmountOfBill = $supplierCosting->payment_amt;

                $isRebateAmountSetting = GeneralSettingEloquentModel::where('setting','enable_rebate_amount_as_item_in_costing')->first();

                if($isRebateAmountSetting && $isRebateAmountSetting->value == 'true' && $supplierCosting->discount_amt > 0){
                    $isRebateAmountEnable = true;
                }else{

                    $isRebateAmountEnable = false;
                }

                if($isGstEnable){

                    if($generalSettingEloquent->value == 'quickbooks'){

                        if($globalTaxCalculation === 'TaxInclusive' ){

                            if($isRebateAmountEnable){

                                $discountPercentage = $supplierCosting->discount_percentage;

                                $firstLineItemAmount = $totalAmountOfBill - $supplierCosting->gst_value;

                                $secondLineItemAmount = $firstLineItemAmount * ($discountPercentage / 100);

                                $netAmountTaxable = $firstLineItemAmount - $secondLineItemAmount;

                                $gstAmount = $netAmountTaxable * 0.09;

                                $finalAmount = $netAmountTaxable + $gstAmount;

                            }else{

                                $firstLineItemAmount = $totalAmountOfBill - $supplierCosting->gst_value;

                                $secondLineItemAmount = 0;

                                $netAmountTaxable = $firstLineItemAmount;

                                $gstAmount = $netAmountTaxable * 0.09;

                                $finalAmount = $netAmountTaxable + $gstAmount;
                            }

                        }else{

                            $discountPercentage = $supplierCosting->discount_percentage;

                            if($discountPercentage > 0){

                                $firstLineItemAmount = $totalAmountOfBill - $supplierCosting->discount_amt;

                                $secondLineItemAmount = $supplierCosting->discount_amt;

                                $gstAmount = $supplierCosting->gst_value;
                            }else{

                                $firstLineItemAmount = $totalAmountOfBill;
                            
                                $secondLineItemAmount = 0;
    
                                $gstAmount = $supplierCosting->gst_value;

                                $finalAmount = $firstLineItemAmount;
                            }

                            $netAmountTaxable = $firstLineItemAmount;
                        }

                    }else{

                        $discountPercentage = $supplierCosting->discount_percentage;

                        if($discountPercentage > 0){

                            $firstLineItemAmount = $totalAmountOfBill - $supplierCosting->discount_amt;

                            $secondLineItemAmount = $supplierCosting->discount_amt;

                            $finalAmount = $totalAmountOfBill;

                        }else{

                            $firstLineItemAmount = $totalAmountOfBill;
                        
                            $secondLineItemAmount = 0;

                            $finalAmount = $totalAmountOfBill;
                        }

                        $gstAmount = $supplierCosting->gst_value;

                        $netAmountTaxable = $firstLineItemAmount;
                    }

                }else{

                    $discountPercentage = $supplierCosting->discount_percentage;

                    if($discountPercentage > 0){

                        $firstLineItemAmount = $totalAmountOfBill;

                        $secondLineItemAmount = $supplierCosting->discount_amt;

                        $finalAmount = $totalAmountOfBill - $supplierCosting->discount_amt;
                    }else{

                        $firstLineItemAmount = $totalAmountOfBill;
                    
                        $secondLineItemAmount = 0;

                        $finalAmount = $totalAmountOfBill;
                    }

                    $gstAmount = 0;

                    $netAmountTaxable = $firstLineItemAmount;                    
                }

                $expenseType = QboExpenseTypeEloquentModel::find($supplierCosting->quick_book_expense_id);

                if($generalSettingEloquent->value == 'quickbooks'){
                    $quickBookExpenseID = $expenseType->quick_book_id;
                }else{
                    $quickBookExpenseID = $expenseType->xero_id;
                }

                $billData = [
                    'billID' => $supplierCostingEloquent->quick_book_bill_id,
                    'vendorID' => $qboVendorId,
                    'userID' => $qboCustomerId,
                    'totalAmount' => $finalAmount,
                    'firtLineItemAmount' => $firstLineItemAmount,
                    'secondLineItemAmount' =>  $isRebateAmountEnable ? -abs($secondLineItemAmount) : 0,
                    'netAmountTaxable' => $netAmountTaxable,
                    'totalTax' => $gstAmount,
                    'isRebateAmountEnable' => $isRebateAmountEnable,
                    'invoiceDate' => $supplierCosting->invoice_date,
                    'description' => $supplierCosting->description,
                    'invoiceNo' => $supplierCosting->invoice_no,
                    'PrivateNote' => $supplierCosting->description,
                    'taxCodeRef' => $taxCodeRef,
                    'taxRateRef' => $taxRateRef,
                    'taxPercent' => $taxPercent,
                    'globalTaxCalculation' => $globalTaxCalculation,
                    'quickBookExpenseID' => $quickBookExpenseID,
                    'classRef' =>  $supplierCostingEloquent->project->quickbook_class_id,
                    'rebateCategoryRef' => $rebateCategoryRef,
                    'isGstEnable' => $isGstEnable,
                    'isTaxCalculationNeeded' => $isTaxCalculationNeeded
                ];

                Log::info('Bill Data:', $billData);

                $this->quickBookService->updateBill($supplierCostingEloquent->project->company_id,$billData);

            } catch (\Exception $e) {

                Log::debug($e);
            }
        }

        return $supplierCostingEloquent;
    }

    public function updateForPaymentId($supplierCostingIds,$paymentId)
    {
        $invoices = SupplierCostingEloquentModel::whereIn('id', $supplierCostingIds)->get();

        foreach ($invoices as $invoice) {

            $invoice->amount_paid = $invoice->amount_paid + $invoice->to_pay;
            $invoice->to_pay = 0;
            $invoice->status = 3;

            $invoice->save();
        }

        return true;

    }

    public function verify(int $id,int $verifyBy)
    {
        $supplierCosting = SupplierCostingEloquentModel::findOrFail($id);

        $directApprove = GeneralSettingEloquentModel::where('setting','enable_direct_approve_supplier_costing')->first();

        if($supplierCosting->status === 1 || $supplierCosting->status === 2 || $supplierCosting->status === 3){

            return false;

        }else{

            $supplierCosting->status = 1;
            $supplierCosting->verify_by = $verifyBy;

            $supplierCosting->save();

            if($directApprove && $directApprove->value == 'true')
            {

                Log::channel('daily')->info("Direct Approve");
                $this->approve($id);
            }

            return true;
        }
    }

    public function approve(int $id)
    {
        $supplierCosting = SupplierCostingEloquentModel::findOrFail($id);

        if($supplierCosting->status === 2 || $supplierCosting->status === 0){

            return false;

        }else{

            $numOfApprovalSetting = GeneralSettingEloquentModel::where('setting','vendor_invoice_number_of_approval_required')->first();

            $numOfApprovalsRequired = !empty($numOfApprovalSetting->value) ? (int)$numOfApprovalSetting->value : 1;

            $numOfApprovals = $supplierCosting->approvals()->count();

            // We + 1 here because this API call itself is consider as 1 more approval
            if(($numOfApprovals + 1 ) >=  $numOfApprovalsRequired)
                $supplierCosting->status = 2;

            SupplierCostingApprovalEloquentModel::firstOrCreate(
                ['vendor_invoice_id' => $id, 'approved_by' => auth()->user()->id]
            );

            $supplierCosting->save();

            return true;
        }
    }

    public function checkSameCompany(array $supplierCostingIds)
    {
        $companyIds = [];

        $invoices = SupplierCostingEloquentModel::with('project.company')->whereIn('id',$supplierCostingIds)->get();

        foreach ($invoices as $invoice) {

            $companyId = $invoice->project->company->id;

            $companyIds[] = $companyId;
        }

        if (reset($companyIds) !== current($companyIds)) {
            return false; // Different company IDs found
        }else{
            return true;
        }
    }

    public function destroy(int $supplier_costing_id) : void
    {
        $supplierCostingEloquent = SupplierCostingEloquentModel::query()->findOrFail($supplier_costing_id);

        if(is_null($supplierCostingEloquent->purchase_order_id))
        {
            $supplierCostingEloquent->delete();

        }else
        {
            // Included deleted at col as the field persists after deleting
            $supplierCostingEloquent->deleted_at = now();

            // NOTE:
            $supplierCostingEloquent->invoice_no = null;

            $supplierCostingEloquent->payment_amt = null;

            $supplierCostingEloquent->discount_percentage = null;

            $supplierCostingEloquent->discount_amt = null;

            $supplierCostingEloquent->credit_amt = null;

            $supplierCostingEloquent->document_file = null;

            $supplierCostingEloquent->save();

        }
    }
}