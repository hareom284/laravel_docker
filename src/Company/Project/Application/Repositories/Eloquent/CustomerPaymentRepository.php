<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use address;
use stdClass;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\Project\Domain\Model\Entities\CustomerPayment;
use Src\Company\Project\Domain\Resources\CustomerPaymentResource;
use Src\Company\Project\Application\Mappers\CustomerPaymentMapper;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Domain\Resources\AllCustomerPaymentResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\PaymentTypeEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\BankInfoEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\AccountingSettingEloquentModel;

class CustomerPaymentRepository implements CustomerPaymentRepositoryInterface
{

    private $accountingService;

    public function __construct(AccountingServiceInterface $accountingService = null)
    {
        $this->accountingService = $accountingService;
    }

    public function index(array $filters)
    {
        $perPage = $filters['perPage'] ?? 10;

        $qboConfig = config('quickbooks');

        $query = CustomerPaymentEloquentModel::query();

        if (isset($filters['projectId'])) {

            $projectId = $filters['projectId'];

            $query->where('sale_report_id', $projectId);
        }else{

            $query->where('sale_report_id', '!=', 0);
        }

        $customerPaymentEloquent = $query->orderBy('id','DESC')->paginate($perPage);

        $customerPayments = AllCustomerPaymentResource::collection($customerPaymentEloquent);

        $links = [
            'first' => $customerPayments->url(1),
            'last' => $customerPayments->url($customerPayments->lastPage()),
            'prev' => $customerPayments->previousPageUrl(),
            'next' => $customerPayments->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $customerPayments->currentPage(),
            'from' => $customerPayments->firstItem(),
            'last_page' => $customerPayments->lastPage(),
            'path' => $customerPayments->url($customerPayments->currentPage()),
            'per_page' => $perPage,
            'to' => $customerPayments->lastItem(),
            'total' => $customerPayments->total(),
        ];
        $responseData['data'] = $customerPayments;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        $responseData['qbo_integration'] = $qboConfig['qbo_integration'];

        return $responseData;
    }

    public function getBySaleReportId($saleReportId)
    {
        $customerPayments = CustomerPaymentEloquentModel::query()
            ->where('sale_report_id', $saleReportId)
            ->get();

        $sortedPayments = $customerPayments
            ->sortBy('index')
            ->sortBy('payment_type')
            ->values();

        $finalResults = CustomerPaymentResource::collection($sortedPayments);

        return $finalResults;
    }

    public function store(CustomerPayment $customerPayment)
    {
        $customerPaymentEloquent = CustomerPaymentMapper::toEloquent($customerPayment);

        $saleReportEloquent = SaleReportEloquentModel::where('id', $customerPaymentEloquent->sale_report_id)->first();
        $totalPreviousCustomerPayment = $saleReportEloquent->customer_payments->sum('amount');

        $isAllowCustomerPaymentPaid = GeneralSettingEloquentModel::where('setting','enable_paid_customer_payment')->where('value', 'true')->first();
        $isEnableProjectStatus = GeneralSettingEloquentModel::where('setting','enable_change_project_status')->where('value', 'true')->first();
        $paymentTypes = PaymentTypeEloquentModel::get();
        $paymentTypeId = $customerPaymentEloquent->payment_type;
        $isDeposit = false;
        $is2ndPayment = false;
        if ($paymentTypes->isNotEmpty()) {
            $isDeposit = $paymentTypeId == $paymentTypes->first()->id; // First item (Deposit)
            $is2ndPayment = $paymentTypeId == $paymentTypes->get(1)?->id; // Second item (2nd Payment)
        }

        $allowCusPaymentOverTotalSales = GeneralSettingEloquentModel::where('setting','allow_cus_payment_over_total_sales')->where('value', 'true')->first();

        if(!$allowCusPaymentOverTotalSales && ($customerPaymentEloquent->amount + $totalPreviousCustomerPayment) > $saleReportEloquent->total_sales){

            return false;
            
        }else{

            if(!$isAllowCustomerPaymentPaid){

                $customerPaymentEloquent->status = 1;

                $saleReportEloquent->update([
                    'paid' => $saleReportEloquent->paid + $customerPaymentEloquent->amount,
                    'remaining' => $saleReportEloquent->remaining - $customerPaymentEloquent->amount
                ]);
                if($isEnableProjectStatus){
                    if($isDeposit){
                        $saleReportEloquent?->project->update([
                            'project_status' => 'Deposit Only'
                        ]);
                    } else if($is2ndPayment){
                        $saleReportEloquent?->project->update([
                            'project_status' => 'On-going'
                        ]);
                    }
                }
            }

            $customerPaymentEloquent->save();

            $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

            if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

                $customerEmail = $saleReportEloquent->project->customer->email;
                $customerNo = $saleReportEloquent->project->customer->contact_no;
                $customerAddress = $saleReportEloquent->project->property->block_num . ' ' . $saleReportEloquent->project->property->street_name . ' ' . $saleReportEloquent->project->property->unit_num;
                $customerPostalCode = $saleReportEloquent->project->property->postal_code;
                $customerType = $saleReportEloquent->project->customer->customers->customer_type ? 1 : 0;

                $checkProjectHasTwoCustomers = $customerPaymentEloquent->saleReport->project->customersPivot;

                $customerName = '';

                if(count($checkProjectHasTwoCustomers) > 1){

                    foreach ($checkProjectHasTwoCustomers as $key => $projectCustomer) {
                        $customerName .= $projectCustomer->first_name . ' ' . $projectCustomer->last_name . ' & ';
                    }
                    
                    // Remove the last ' & ' from the string
                    $customerName = rtrim($customerName, ' & ');

                    $accountingSoftwareCustomerWithId = $this->accountingService->getCustomer($saleReportEloquent->project->company_id,$customerName);

                    if(!$accountingSoftwareCustomerWithId){

                        $customerData = [
                            'name' => $customerName,
                            'companyName' => ($customerType === 1) ? $customerName : null,
                            'email' => $customerEmail,
                            'address' => $customerAddress,
                            'postal_code' => $customerPostalCode,
                            'contact_no' => $customerNo
                        ];

                        $qboCustomerId = $this->accountingService->storeCustomer($saleReportEloquent->project->company_id,$customerData);

                    }else{

                        $qboCustomerId = $accountingSoftwareCustomerWithId;
                    }

                }else{

                    $customerFirstName = $customerPaymentEloquent->saleReport->project->customer->first_name;
                    $customerLastName = $customerPaymentEloquent->saleReport->project->customer->last_name;

                    if(is_null($customerLastName) || $customerLastName == ''){
                        $customerName = $customerFirstName;
                    }else{
                        $customerName = $customerFirstName . ' ' . $customerLastName;
                    }

                    if($generalSettingEloquent->value == 'quickbooks'){
                        $existsQboCustomerId = $customerPaymentEloquent->saleReport->project->customer->quick_book_user_id;
                    }else{
                        $existsQboCustomerId = $customerPaymentEloquent->saleReport->project->customer->xero_user_id;
                    }

                    if(is_null($existsQboCustomerId)){

                        $accountingSoftwareCustomerWithId = $this->accountingService->getCustomer($saleReportEloquent->project->company_id,$customerName);

                        if(!$accountingSoftwareCustomerWithId){

                            $customerData = [
                                'name' => $customerName,
                                'companyName' => ($customerType === 1) ? $customerName : null,
                                'email' => $customerEmail,
                                'address' => $customerAddress,
                                'postal_code' => $customerPostalCode,
                                'contact_no' => $customerNo
                            ];
    
                            $qboCustomerId = $this->accountingService->storeCustomer($saleReportEloquent->project->company_id,$customerData);
                            
                            if($generalSettingEloquent->value == 'quickbooks'){

                                UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                    'quick_book_user_id' => $qboCustomerId
                                ]);

                            }else{

                                UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                    'xero_user_id' => $qboCustomerId
                                ]);
                            }

                        }else{

                            $qboCustomerId = $accountingSoftwareCustomerWithId;

                            if($generalSettingEloquent->value == 'quickbooks'){

                                UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                    'quick_book_user_id' => $qboCustomerId
                                ]);

                            }else{

                                UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                    'xero_user_id' => $qboCustomerId
                                ]);
                            }
                        }

                    }else{

                        $qboCustomerId = $existsQboCustomerId;
                    }
                }

                try {
                    $isGstEnable = is_null($saleReportEloquent->project->company->gst_reg_no) ? false : true ;

                    $taxInclusiveAmt = $customerPaymentEloquent->amount;

                    $netAmountTaxable = $isGstEnable ? round($taxInclusiveAmt / 1.09, 2) : $customerPaymentEloquent->amount ;

                    $totalTax = $isGstEnable ? round($taxInclusiveAmt - $netAmountTaxable, 2) : 0;

                    $accountingSettings = AccountingSettingEloquentModel::where('company_id',$saleReportEloquent->project->company_id)->get();

                    $taxCodeRef = optional($accountingSettings->where('setting', 'taxCodeOfInvoice')->first())->value;
                    $taxRateRef = optional($accountingSettings->where('setting', 'taxRateOfInvoice')->first())->value;
                    $itemRef =  optional($accountingSettings->where('setting', 'InvoiceServices')->first())->value;

                    $invoiceData = [
                        'netAmountTaxable' => $netAmountTaxable,
                        'taxInclusiveAmt' => $taxInclusiveAmt,
                        'totalTax' => $totalTax,
                        'taxCodeRef' => $taxCodeRef,
                        'taxRateRef' => $taxRateRef,
                        'TaxPercent' => $isGstEnable ? 9 : 0,
                        'customerId' => $qboCustomerId,
                        'itemRef' => $itemRef,
                        'name' => $customerPaymentEloquent->description,
                        'remark' => $customerPaymentEloquent->remark,
                        'classRef' => $saleReportEloquent->project->quickbook_class_id,
                        'isGstEnable' => $isGstEnable,
                        'invoiceDate' => $customerPayment->invoice_date,
                    ];

                    Log::info("Stoe Invoice Data: " . json_encode($invoiceData));

                    $quickBookInvoice = $this->accountingService->storeInvoice($saleReportEloquent->project->company_id,$invoiceData);

                    if($generalSettingEloquent->value == 'quickbooks'){

                        $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$quickBookInvoice->Id);

                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'quick_book_invoice_id' => $quickBookInvoice->Id,
                            'invoice_no' => $quickBookInvoice->DocNumber,
                            'unpaid_invoice_file_path' => $qboInvoiceFilePath
                        ]);

                    }else{
                        $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$quickBookInvoice->getInvoiceId());

                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'xero_invoice_id' => $quickBookInvoice->getInvoiceId(),
                            'invoice_no' => $quickBookInvoice->getInvoiceNumber(),
                            'unpaid_invoice_file_path' => $qboInvoiceFilePath
                        ]);
                    }

                } catch (\Exception $e) {

                    throw $e;
                }
            }else{

                $customerInvoiceRunningNumberSetting = GeneralSettingEloquentModel::where('setting','enable_customer_invoice_running_number')->first()->value ?? null;

                $current_folder_name = config('folder.company_folder_name');
                $folder_name  = $current_folder_name;
                $enable_show_last_name_first = GeneralSettingEloquentModel::where('setting', 'enable_show_last_name_first')->first();
                $company = $customerPaymentEloquent->saleReport->project->company;
                $invoice_prefix = $company?->invoice_prefix ?? 'INV';
                $project = $customerPaymentEloquent->saleReport->project;
                $salesperson = $project->salespersons->first();
                $name = $salesperson->first_name . ' ' . $salesperson->last_name;
    
                $initialSalespersonName = implode("", array_map(fn($word) => $word[0], explode(" ", $name)));
                $block_num = $project->property->block_num;
                $block_num = str_replace(array('Blk', 'Blk ', 'Block', 'Block ', 'blk', 'blk ', 'BLK', 'BLK ', 'BLOCK', 'BLOCK '), '', $block_num);
                $totalCustomerPayment = CustomerPaymentEloquentModel::where('sale_report_id', $customerPaymentEloquent->sale_report_id)->count();
                
                if($customerInvoiceRunningNumberSetting == 'true')
                {

                    $initialcustomerInvoiceRunningNumberValue = GeneralSettingEloquentModel::where('setting','customer_invoice_running_number_values')->first()->value ?? null;

                    // Assuming $tempValues is an associative array
                    $customerInvoiceRunningNumberValue = isset($initialcustomerInvoiceRunningNumberValue)
                    ? explode(',', $initialcustomerInvoiceRunningNumberValue)
                    : [];

                    $currentMonth = Carbon::now()->month;
                    $currentMonthInvoiceRunningNumber = (int)$customerInvoiceRunningNumberValue[$currentMonth - 1] + 1;

                    $customerInvoiceRunningNumberValue[$currentMonth - 1] = $currentMonthInvoiceRunningNumber;

                    $initialcustomerInvoiceRunningNumberValue = GeneralSettingEloquentModel::where('setting','customer_invoice_running_number_values')
                                                                ->update(['value' => implode(',', $customerInvoiceRunningNumberValue)]);
                    // $invoiceNumber = $invoice_prefix . Carbon::now()->year .''. Carbon::now()->format('m') .''. str_pad($currentMonthInvoiceRunningNumber, 2, '0', STR_PAD_LEFT);
                    // $customerPaymentInvNo = $invoiceNumber;
                    $customerPaymentInvNo = generateAgreementNumber('customer_invoice', [
                        'company_initial' => $project->company->docu_prefix,
                        'quotation_initial' => $project->company->quotation_prefix,
                        'salesperson_initial' => $initialSalespersonName,
                        'block_num' => $project->property->block_num ?? null,
                        'date' => Carbon::now()->toDateString(),
                        'running_num' => $project->company->invoice_no_start,
                        'project_id' => $project->id,
                        'quotation_num' => $project->company->quotation_no,
                        'project_agr_no' => $project->agreement_no,
                        'invoice_no' => $currentMonthInvoiceRunningNumber,
                        'invoice_initial' => $invoice_prefix,
                        'total_customer_payment' => $totalCustomerPayment,
                        'invoice_running_num' => $project->company->invoice_running_number
                    ]);
                    $project->company->increment('invoice_running_number');
                } else {

                    $initialcustomerInvoiceRunningNumberValue = $company->customer_invoice_running_number_values;
                    if ($company->enable_customer_running_number_by_month) {
                        $customerInvoiceRunningNumberValue = isset($initialcustomerInvoiceRunningNumberValue)
                            ? explode(',', $initialcustomerInvoiceRunningNumberValue)
                            : [];

                        $currentMonth = Carbon::now()->month;
                        $currentMonthInvoiceRunningNumber = (int)$customerInvoiceRunningNumberValue[$currentMonth - 1] + 1;

                        $customerInvoiceRunningNumberValue[$currentMonth - 1] = $currentMonthInvoiceRunningNumber;

                        $company->update([
                            'customer_invoice_running_number_values' => implode(',', $customerInvoiceRunningNumberValue)
                        ]);
                        // $invoiceNumber = $invoice_prefix . Carbon::now()->year . '' . Carbon::now()->format('m') . '' . str_pad($currentMonthInvoiceRunningNumber, 2, '0', STR_PAD_LEFT);
                        // $customerPaymentInvNo = $invoiceNumber;
                        $customerPaymentInvNo = generateAgreementNumber('customer_invoice', [
                            'company_initial' => $project->company->docu_prefix,
                            'quotation_initial' => $project->company->quotation_prefix,
                            'salesperson_initial' => $initialSalespersonName,
                            'block_num' => $project->property->block_num ?? null,
                            'date' => Carbon::now()->toDateString(),
                            'running_num' => $project->company->invoice_no_start,
                            'project_id' => $project->id,
                            'quotation_num' => $project->company->quotation_no,
                            'project_agr_no' => $project->agreement_no,
                            'invoice_no' => $currentMonthInvoiceRunningNumber,
                            'invoice_initial' => $invoice_prefix,
                            'total_customer_payment' => $totalCustomerPayment,
                            'invoice_running_num' => $project->company->invoice_running_number
                        ]);
                        $project->company->increment('invoice_running_number');
                    } else{
                        $prjInvoiceNo = $customerPaymentEloquent->saleReport->project->invoice_no ?? 1;


                        // $paddedInvoiceNo = str_pad($prjInvoiceNo, 4, '0', STR_PAD_LEFT);

                        // $customerPaymentInvNo = $invoice_prefix . $paddedInvoiceNo . '.' . $totalCustomerPayment;
                        $customerPaymentInvNo = generateAgreementNumber('customer_invoice', [
                            'company_initial' => $project->company->docu_prefix,
                            'quotation_initial' => $project->company->quotation_prefix,
                            'salesperson_initial' => $initialSalespersonName,
                            'block_num' => $project->property->block_num ?? null,
                            'date' => Carbon::now()->toDateString(),
                            'running_num' => $project->company->invoice_no_start,
                            'project_id' => $project->id,
                            'quotation_num' => $project->company->quotation_no,
                            'project_agr_no' => $project->agreement_no,
                            'invoice_no' => $prjInvoiceNo,
                            'invoice_initial' => $invoice_prefix,
                            'total_customer_payment' => $totalCustomerPayment,
                            'invoice_running_num' => $project->company->invoice_running_number

                        ]);
                        $project->company->increment('invoice_running_number');

                    }
                }

                $companies = [
                    "company_logo" => $this->getCompanyLogo($customerPaymentEloquent->saleReport->project->company->logo),
                ];

                if((int) $customerPaymentEloquent->saleReport->project->company->gst !== 0){

                    $netAmountTaxable = round($customerPaymentEloquent->amount / 1.09, 2);

                    $gst = round($customerPaymentEloquent->amount - $netAmountTaxable, 2);
                    $isGstInclude = true;
                }else{
                    $netAmountTaxable = $customerPaymentEloquent->amount;
                    $gst = 0;
                    $isGstInclude = false;
                }
                $fullAddress = $customerPaymentEloquent->saleReport->project->property ?? null;
                $blockNum =  $customerPaymentEloquent->saleReport->project->property->block_num ?? null;
                $streetName = $customerPaymentEloquent->saleReport->project->property->street_name ?? null;
                $unitNum = $customerPaymentEloquent->saleReport->project->property->unit_num ?? null;

                $agreementNo = $customerPaymentEloquent->saleReport->project->agreement_no ?? null;
                $contractPrice = $customerPaymentEloquent->saleReport->total_sales ?? null;
                $company = $customerPaymentEloquent->saleReport->project->company ?? null;

                $salepersonLists = [];

                foreach ($customerPaymentEloquent->saleReport->project->salespersons as $saleperson) {
                    $sale = [
                        'contact_no' => $saleperson->contact_no ?? '-',
                        'email' => $saleperson->email ?? '-',
                        'rank_name' => $saleperson->staffs->rank->rank_name ?? '-', // Handle null values
                        'full_name' => trim($saleperson->name_prefix . ' ' . $saleperson->first_name . ' ' . $saleperson->last_name), // Remove extra spaces
                    ];
                    $salepersonLists[] = $sale;
                }

                $address = $blockNum . ' ' . $streetName . ' ' . $unitNum;
                $total_sales_amount = $saleReportEloquent->total_sales;
                $paid_amount = $saleReportEloquent->paid;
                $remaining_amount = $saleReportEloquent->remaining;
                $sortData = generateContractItems($saleReportEloquent->project_id);
                $voSortData = generateVOItems($saleReportEloquent->project_id);
                $qoDiscountAmount = calculateTotalDiscountAmountHelper($saleReportEloquent->project_id, 'QUOTATION');
                $voDiscountAmount = calculateTotalDiscountAmountHelper($saleReportEloquent->project_id, 'VARIATIONORDER');

                $data = [
                    'customers_array' => $customerPaymentEloquent->saleReport->project->customersPivot->toArray(),
                    'company_name' => $company->name,
                    'address' => $address,
                    'type' => $customerPaymentEloquent->paymentType->name,
                    'amount' => $customerPaymentEloquent->amount,
                    'customerPaymentInvNo' => $customerPaymentInvNo,
                    'payment_date' => $customerPaymentEloquent->created_at->format('d/m/Y'),
                    'description' => $customerPaymentEloquent->description,
                    'remark' => $customerPaymentEloquent->remark,
                    'netAmountTaxable' => $netAmountTaxable,
                    'gst' => $gst,
                    'gst_inclusive' => $isGstInclude,
                    'agreementNo' => $agreementNo,
                    'contractPrice' => $contractPrice,
                    'status' => $customerPaymentEloquent->status,
                    'salepersons' => $salepersonLists,
                    'company_name' => $company->name,
                    'total_sales_amount' => $total_sales_amount,
                    'paid_amount' => $paid_amount,
                    'remaining_amount' => $remaining_amount,
                    'sortQuotation' => $sortData,
                    'sortVariation' => $voSortData,
                    'qoDiscountAmount' => $qoDiscountAmount,
                    'voDiscountAmount' => $voDiscountAmount
                ];

                $headerFooterData = [
                    'companies' => $companies,
                    'company_name' => $company->name,
                    'company_email' => $company->email,
                    'company_address' => $company->main_office,
                    'company_tel' => $company->tel,
                    'company_fax' => $company->fax,
                    'company_reg_no' => $company->reg_no,
                    'properties' => $fullAddress,
                    'customers_array' => $customerPaymentEloquent->saleReport->project->customersPivot->toArray(),
                    'enable_show_last_name_first' => $enable_show_last_name_first->value ?? null,
                    'payment_date' => $customerPaymentEloquent->created_at->format('d/m/Y'),
                    'created_at' => $this->convertDate($customerPaymentEloquent->created_at->format('d/m/Y')),
                    'document_agreement_no' => $agreementNo,
                    'customerPaymentInvNo' => $customerPaymentInvNo,
                    'salepersons' => $salepersonLists,
                    'footer_text' => $company->document_standards?->footer_text
                ];

                if($folder_name === 'Tidplus' || $folder_name === 'Whst' || $folder_name === 'Tag' || $folder_name === 'Henglai' || $folder_name === 'Molecule' || $folder_name === 'Metis' || $folder_name === 'Ideology'){

                    if (!$isAllowCustomerPaymentPaid) {
                        $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                        $paidFilePath = $this->downloadInvoicePdf($folder_name, '.paid_invoice', $data, $headerFooterData);
                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'paid_invoice_file_path' => $paidFilePath,
                            'unpaid_invoice_file_path' => $unpaidFilePath,
                            'invoice_no' => $customerPaymentInvNo
                        ]);
                    } else {
                        $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'unpaid_invoice_file_path' => $unpaidFilePath,
                            'invoice_no' => $customerPaymentInvNo
                        ]);
                    }
                } else {
                    $folder_name = 'Molecule';
                    if (!$isAllowCustomerPaymentPaid) {
                        $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                        $paidFilePath = $this->downloadInvoicePdf($folder_name, '.paid_invoice', $data, $headerFooterData);
                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'paid_invoice_file_path' => $paidFilePath,
                            'unpaid_invoice_file_path' => $unpaidFilePath,
                            'invoice_no' => $customerPaymentInvNo
                        ]);
                    } else {
                        $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'unpaid_invoice_file_path' => $unpaidFilePath,
                            'invoice_no' => $customerPaymentInvNo
                        ]);
                    }
                }

            }

            return CustomerPaymentMapper::fromEloquent($customerPaymentEloquent);
        }
    }

    public function importFromQbo(int $projectId)
    {
        $project = ProjectEloquentModel::with('saleReport')->find($projectId);

        $saleReportId = $project->saleReport->id;

        if(is_null($project->quickbook_class_id)){

            $classFromQbo = $this->accountingService->getProjectByName($project->company_id, $project->agreement_no);

            $qboClassId = is_null($classFromQbo) ? null : $classFromQbo->Id;

            if(!is_null($qboClassId)){
                $project->quickbook_class_id = $classFromQbo->Id;
                $project->save();
            }
        }else{
            $qboClassId = $project->quickbook_class_id;
        }


        if(!is_null($qboClassId)){

            $customerPayments = CustomerPaymentEloquentModel::where('remark', $qboClassId)->where('sale_report_id', 0)->get();

            foreach ($customerPayments as $customerPayment) {

                if($customerPayment->is_sale_receipt == 1){

                    $pdf = $this->accountingService->saveSaleReceiptPdf($project->company_id, $customerPayment->quick_book_invoice_id);
                    $customerPayment->paid_invoice_file_path = $pdf;
                }else{
                    $unpaidPdf = $this->accountingService->saveInvoicePdf($project->company_id, $customerPayment->quick_book_invoice_id);
                    $customerPayment->unpaid_invoice_file_path = $unpaidPdf;
                }

                $customerPayment->sale_report_id = $saleReportId;
                $customerPayment->customer_id = $project->customer_id;

                $customerPayment->save();
            }
        }

        return true;
    }

    public function storeWithQbo(int $companyId)
    {
        $invoicesFormQbo = $this->accountingService->getInvoiceByCompanyId($companyId);

        if(is_null($invoicesFormQbo)){
            throw new Exception("This Customer Does Not Have Invoices In QBO");
        }else{

            foreach ($invoicesFormQbo as $invoice) {

                CustomerPaymentEloquentModel::create([
                    'payment_type' => 1,
                    'amount' => $invoice->TotalAmt,
                    'description' => "Invoice From QBO",
                    'remark' => $invoice->Line[0]->SalesItemLineDetail->ClassRef,
                    'index' => 1,
                    'status' => 0,
                    'sale_report_id' => 0,
                    'quick_book_invoice_id' => $invoice->Id,
                    'invoice_no' => $invoice->DocNumber,
                    'customer_id' => 0,
                ]);
            }
        }

        return true;
    }

    public function storeSaleReceiptWithQbo(int $companyId)
    {
        $saleReceiptsFromQbo = $this->accountingService->getSaleReceiptByCompanyId($companyId);

        if(is_null($saleReceiptsFromQbo)){
            throw new Exception("This Company Does Not Have Sale Receipts In QBO");
        }else{

            foreach ($saleReceiptsFromQbo as $saleReceipt) {

                CustomerPaymentEloquentModel::create([
                    'payment_type' => 1,
                    'amount' => $saleReceipt->TotalAmt,
                    'description' => "Invoice From QBO",
                    'remark' => $saleReceipt->Line[0]->SalesItemLineDetail->ClassRef,
                    'index' => 1,
                    'status' => 2,
                    'sale_report_id' => 0,
                    'quick_book_invoice_id' => $saleReceipt->Id,
                    'invoice_no' => $saleReceipt->DocNumber,
                    'customer_id' => 0,
                    'is_sale_receipt' => true,
                ]);
            }
        }

        return true;
    }

    public function update(CustomerPayment $customerPayment)
    {
        $oldCustomerPayment = CustomerPaymentEloquentModel::where('id', $customerPayment->id)->first();

        $customerPaymentEloquent = CustomerPaymentMapper::toEloquent($customerPayment);
        $finalCustomerPayment = CustomerPaymentEloquentModel::where('sale_report_id', $customerPaymentEloquent->sale_report_id)->orderBy('payment_type', 'asc')->get()->last();
        $saleReportEloquent = SaleReportEloquentModel::where('id', $customerPaymentEloquent->sale_report_id)->first();

        $isEnableProjectStatus = GeneralSettingEloquentModel::where('setting','enable_change_project_status')->where('value', 'true')->first();
        $paymentTypes = PaymentTypeEloquentModel::get();
        $paymentTypeId = $customerPaymentEloquent->payment_type;
        $isDeposit = false;
        $is2ndPayment = false;

        if ($paymentTypes->isNotEmpty()) {
            $isDeposit = $paymentTypeId == $paymentTypes->first()->id;
            $is2ndPayment = $paymentTypeId == $paymentTypes->get(1)?->id;
        }
        DB::beginTransaction();

        if($customerPaymentEloquent->status == 1){
            $saleReportEloquent->update([
                'paid' => $saleReportEloquent->paid + $customerPaymentEloquent->amount,
                'remaining' => $saleReportEloquent->remaining - $customerPaymentEloquent->amount
            ]);
            if($isEnableProjectStatus){
                if($isDeposit){
                    $saleReportEloquent?->project->update([
                        'project_status' => 'Deposit Only'
                    ]);
                } else if($is2ndPayment){
                    $saleReportEloquent?->project->update([
                        'project_status' => 'On-going'
                    ]);
                }
            }
        }

        // $customerPaymentEloquent->description = $oldCustomerPayment->description;
        $customerPaymentEloquent->save();

        $totalCustomerPayment = CustomerPaymentEloquentModel::where('sale_report_id', $customerPaymentEloquent->sale_report_id)->sum('amount');

        $allowCusPaymentOverTotalSales = GeneralSettingEloquentModel::where('setting','allow_cus_payment_over_total_sales')->where('value', 'true')->first();

        if(!$allowCusPaymentOverTotalSales && $totalCustomerPayment > $saleReportEloquent->total_sales){

            DB::rollBack();

            return [
                'status' => false,
                'data' => 'Total customer payment amount exceeds the total sales amount.'
            ];

        }else{

            DB::commit();
        }

        /*
        if ($oldCustomerPayment->amount > $customerPayment->amount && $customerPayment->id != $finalCustomerPayment->id){
            $extraAmount = $oldCustomerPayment->amount - $customerPayment->amount;
            $finalCustomerPayment->update([
                'amount' => $finalCustomerPayment->amount + $extraAmount
            ]);
        } else if($customerPayment->amount > $oldCustomerPayment->amount && $customerPayment->id != $finalCustomerPayment->id){
            $extraAmount = $customerPayment->amount - $oldCustomerPayment->amount;
            $finalCustomerPayment->update([
                'amount' => $finalCustomerPayment->amount - $extraAmount
            ]);
        }
        */

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            $qboCustomerId = $customerPaymentEloquent->saleReport->project->customer->quick_book_user_id;

            try {

                if($customerPaymentEloquent->status === 1){

                    if($generalSettingEloquent->value == 'quickbooks'){

                        if(!is_null($oldCustomerPayment->quick_book_invoice_id)){

                            $bankInfo = !is_null($customerPayment->bank_info) ? BankInfoEloquentModel::find($customerPayment->bank_info) : null;

                            $paymentData = [
                                'amount' => $customerPaymentEloquent->amount,
                                'customerId' => $qboCustomerId,
                                'invoiceId' => $oldCustomerPayment->quick_book_invoice_id,
                                'DepositToAccountRef' => $bankInfo ? $bankInfo->quick_book_account_id : null,
                                'invoiceDate' => $customerPayment->invoice_date,
                            ];

                            $quickBookPayment = $this->accountingService->storePayment($saleReportEloquent->project->company_id,$paymentData);

                            $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$oldCustomerPayment->quick_book_invoice_id);

                            $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                                'quick_book_payment_id' => $quickBookPayment->Id,
                                'invoice_no' => $oldCustomerPayment->invoice_no,
                                'description' => $oldCustomerPayment->description,
                                'paid_invoice_file_path' => $qboInvoiceFilePath
                            ]);
                        }

                    }else{

                        if(!is_null($oldCustomerPayment->xero_invoice_id)){

                            $xeroAccountId = BankInfoEloquentModel::find(1);

                            $paymentData = [
                                'amount' => $customerPaymentEloquent->amount,
                                'invoiceId' => $oldCustomerPayment->xero_invoice_id,
                                'accountId' => $xeroAccountId->xero_account_id,
                            ];

                            $xeroPayment = $this->accountingService->storePayment($saleReportEloquent->project->company_id,$paymentData);

                            $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$oldCustomerPayment->xero_invoice_id);

                            $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                                'xero_payment_id' => $xeroPayment->getPaymentId(),
                                'invoice_no' => $oldCustomerPayment->invoice_no,
                                'description' => $oldCustomerPayment->description,
                                'paid_invoice_file_path' => $qboInvoiceFilePath
                            ]);
                        }
                    }

                }else{

                    if(!is_null($oldCustomerPayment->quick_book_invoice_id)){
                        
                        $isGstEnable = is_null($saleReportEloquent->project->company->gst_reg_no) ? false : true ;

                        $taxInclusiveAmt = $customerPaymentEloquent->amount;

                        $netAmountTaxable = $isGstEnable ? round($taxInclusiveAmt / 1.09, 2) : $customerPaymentEloquent->amount ;

                        $totalTax = $isGstEnable ? round($taxInclusiveAmt - $netAmountTaxable, 2) : 0;

                        $accountingSettings = AccountingSettingEloquentModel::where('company_id',$saleReportEloquent->project->company_id)->get();
    
                        $taxCodeRef = optional($accountingSettings->where('setting', 'taxCodeOfInvoice')->first())->value;
                        $taxRateRef = optional($accountingSettings->where('setting', 'taxRateOfInvoice')->first())->value;
                        $itemRef =  optional($accountingSettings->where('setting', 'InvoiceServices')->first())->value;

                        $invoiceData = [
                            'quickBookId' => $oldCustomerPayment->quick_book_invoice_id,
                            'netAmountTaxable' => $netAmountTaxable,
                            'taxInclusiveAmt' => $taxInclusiveAmt,
                            'totalTax' => $totalTax,
                            'taxCodeRef' => $taxCodeRef,
                            'taxRateRef' => $taxRateRef,
                            'customerId' => $qboCustomerId,
                            'name' => $customerPaymentEloquent->description,
                            'remark' => $customerPaymentEloquent->remark,
                            'classRef' => $saleReportEloquent->project->quickbook_class_id,
                            'isGstEnable' => $isGstEnable,
                            'itemRef' => $itemRef,
                            'invoiceDate' => $customerPayment->invoice_date,
                        ];

                        Log::info("Update Invoice Data: " . json_encode($invoiceData));

                        $quickBookInvoice = $this->accountingService->updateInvoice($saleReportEloquent->project->company_id,$invoiceData);

                        $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$quickBookInvoice->Id);

                        $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                            'quick_book_invoice_id' => $quickBookInvoice->Id,
                            'unpaid_invoice_file_path' => $qboInvoiceFilePath
                        ]);
                    }else{

                        $customerEmail = $saleReportEloquent->project->customer->email;
                        $customerNo = $saleReportEloquent->project->customer->contact_no;
                        $customerAddress = $saleReportEloquent->project->property->block_num . ' ' . $saleReportEloquent->project->property->street_name . ' ' . $saleReportEloquent->project->property->unit_num;
                        $customerPostalCode = $saleReportEloquent->project->property->postal_code;
                        $customerType = $saleReportEloquent->project->customer->customers->customer_type ? 1 : 0;
        
                        $checkProjectHasTwoCustomers = $customerPaymentEloquent->saleReport->project->customersPivot;
        
                        $customerName = '';

                        if(count($checkProjectHasTwoCustomers) > 1){

                            foreach ($checkProjectHasTwoCustomers as $key => $projectCustomer) {

                                $projectCustomerFirstName = $projectCustomer->first_name;
                                $projectCustomerLastName = $projectCustomer->last_name;

                                if(is_null($projectCustomerLastName) || $projectCustomerLastName == ''){
                                    $projectCustomerName = $projectCustomerFirstName;
                                }else{
                                    $projectCustomerName = $projectCustomerFirstName . ' ' . $projectCustomerLastName;
                                }

                                $customerName .=  $projectCustomerName . ' & ';
                            }
                            
                            // Remove the last ' & ' from the string
                            $customerName = rtrim($customerName, ' & ');
        
                            $accountingSoftwareCustomerWithId = $this->accountingService->getCustomer($saleReportEloquent->project->company_id,$customerName);

                            if(!$accountingSoftwareCustomerWithId){

                                $customerData = [
                                    'name' => $customerName,
                                    'companyName' => ($customerType === 1) ? $customerName : null,
                                    'email' => $customerEmail,
                                    'address' => $customerAddress,
                                    'postal_code' => $customerPostalCode,
                                    'contact_no' => $customerNo
                                ];
        
                                $qboCustomerId = $this->accountingService->storeCustomer($saleReportEloquent->project->company_id,$customerData);
        
                            }else{
        
                                $qboCustomerId = $accountingSoftwareCustomerWithId;
                            }
                        }else{
        
                            if($generalSettingEloquent->value == 'quickbooks'){
                                $existsQboCustomerId = $oldCustomerPayment->saleReport->project->customer->quick_book_user_id;
                            }else{
                                $existsQboCustomerId = $oldCustomerPayment->saleReport->project->customer->xero_user_id;
                            }

                            if(is_null($existsQboCustomerId)){

                                $customerFirstName = $saleReportEloquent->project->customer->first_name;
                                $customerLastName = $saleReportEloquent->project->customer->last_name;

                                $customerName = '';

                                if(is_null($customerLastName) || $customerLastName == ''){
                                    $customerName = $customerFirstName;
                                }else{
                                    $customerName = $customerFirstName . ' ' . $customerLastName;
                                }

                                $accountingSoftwareCustomerWithId = $this->accountingService->getCustomer($saleReportEloquent->project->company_id,$customerName);
    
                                if(!$accountingSoftwareCustomerWithId){
    
                                    $customerData = [
                                        'name' => $customerName,
                                        'companyName' => ($customerType === 1) ? $customerName : null,
                                        'email' => $customerEmail,
                                        'address' => $customerAddress,
                                        'postal_code' => $customerPostalCode,
                                        'contact_no' => $customerNo
                                    ];
    
                                    $qboCustomerId = $this->accountingService->storeCustomer($saleReportEloquent->project->company_id,$customerData);

                                    if($generalSettingEloquent->value == 'quickbooks'){

                                        UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                            'quick_book_user_id' => $qboCustomerId
                                        ]);

                                    }else{

                                        UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                            'xero_user_id' => $qboCustomerId
                                        ]);
                                    }
    
                                }else{

                                    $qboCustomerId = $accountingSoftwareCustomerWithId;

                                    if($generalSettingEloquent->value == 'quickbooks'){

                                        UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                            'quick_book_user_id' => $qboCustomerId
                                        ]);

                                    }else{

                                        UserEloquentModel::find($saleReportEloquent->project->customer->id)->update([
                                            'xero_user_id' => $qboCustomerId
                                        ]);
                                    }
                                }
                            }else{

                                $qboCustomerId = $existsQboCustomerId;
                            }
                        }

                        try {

                            $isGstEnable = ($saleReportEloquent->project->company->gst === 0) && is_null($saleReportEloquent->project->company->gst_reg_no);

                            $taxInclusiveAmt = $customerPaymentEloquent->amount;

                            $netAmountTaxable = $isGstEnable ? round($taxInclusiveAmt / 1.09, 2) : $customerPaymentEloquent->amount ;

                            $totalTax = $isGstEnable ? round($taxInclusiveAmt - $netAmountTaxable, 2) : 0;

                            $invoiceData = [
                                'netAmountTaxable' => $netAmountTaxable,
                                'taxInclusiveAmt' => $taxInclusiveAmt,
                                'totalTax' => $totalTax,
                                'taxCodeRef' => $isGstEnable ? 57 : 20,
                                'taxRateRef' => $isGstEnable ? 45 : 17,
                                'TaxPercent' => $isGstEnable ? 9 : 0,
                                'customerId' => $qboCustomerId,
                                'name' => $customerPaymentEloquent->description,
                                'remark' => $customerPaymentEloquent->remark,
                                'classRef' => $saleReportEloquent->project->quickbook_class_id,
                                'isGstEnable' => $isGstEnable,
                                'invoiceDate' => $customerPayment->invoice_date,
                            ];

                            $quickBookInvoice = $this->accountingService->storeInvoice($saleReportEloquent->project->company_id,$invoiceData);

                            if($generalSettingEloquent->value == 'quickbooks'){

                                $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$quickBookInvoice->Id);

                                $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                                    'quick_book_invoice_id' => $quickBookInvoice->Id,
                                    'invoice_no' => $quickBookInvoice->DocNumber,
                                    'unpaid_invoice_file_path' => $qboInvoiceFilePath
                                ]);

                            }else{
                                $qboInvoiceFilePath = $this->accountingService->saveInvoicePdf($saleReportEloquent->project->company_id,$quickBookInvoice->getInvoiceId());

                                $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                                    'xero_invoice_id' => $quickBookInvoice->getInvoiceId(),
                                    'invoice_no' => $quickBookInvoice->getInvoiceNumber(),
                                    'unpaid_invoice_file_path' => $qboInvoiceFilePath
                                ]);
                            }

                        } catch (\Exception $e) {

                            throw $e;
                        }
                    }
                }
            } catch (\Exception $e) {

                return [
                    'status' => false,
                    'data' => $e->getMessage()
                ];
            }
        }else{
                $current_folder_name = config('folder.company_folder_name');
                $folder_name  = $current_folder_name;
                $enable_show_last_name_first = GeneralSettingEloquentModel::where('setting', 'enable_show_last_name_first')->first();

                if((int) $customerPaymentEloquent->saleReport->project->company->gst !== 0){

                    $netAmountTaxable = round($customerPaymentEloquent->amount / 1.09, 2);

                    $gst = round($customerPaymentEloquent->amount - $netAmountTaxable, 2);
                    $isGstInclude = true;
                }else{
                    $netAmountTaxable = $customerPaymentEloquent->amount;
                    $gst = 0;
                    $isGstInclude = false;
                }

                $companies = [
                    "company_logo" => $this->getCompanyLogo($customerPaymentEloquent->saleReport->project->company->logo),
                ];
                $fullAddress = $customerPaymentEloquent->saleReport->project->property ?? null;
                $blockNum =  $customerPaymentEloquent->saleReport->project->property->block_num ?? null;
                $streetName = $customerPaymentEloquent->saleReport->project->property->street_name ?? null;
                $unitNum = $customerPaymentEloquent->saleReport->project->property->unit_num ?? null;

                $address = $blockNum . ' ' . $streetName . ' ' . $unitNum;

                $agreementNo = $customerPaymentEloquent->saleReport->project->agreement_no ?? null;
                $contractPrice = $customerPaymentEloquent->saleReport->total_sales ?? null;
                $company = $customerPaymentEloquent->saleReport->project->company ?? null;

                $salepersonLists = [];
                foreach ($customerPaymentEloquent->saleReport->project->salespersons as $saleperson) {
                    $sale = [
                        'contact_no' => $saleperson->contact_no ?? '-',
                        'email' => $saleperson->email ?? '-',
                        'rank_name' => $saleperson->staffs->rank->rank_name ?? '-', // Handle null values
                        'full_name' => trim($saleperson->name_prefix . ' ' . $saleperson->first_name . ' ' . $saleperson->last_name), // Remove extra spaces
                    ];
                    $salepersonLists[] = $sale;
                }

                $total_sales_amount = $saleReportEloquent->total_sales;
                $paid_amount = $saleReportEloquent->paid;
                $remaining_amount = $saleReportEloquent->remaining;
                $sortData = generateContractItems($saleReportEloquent->project_id);
                $voSortData = generateVOItems($saleReportEloquent->project_id);
                $qoDiscountAmount = calculateTotalDiscountAmountHelper($saleReportEloquent->project_id, 'QUOTATION');
                $voDiscountAmount = calculateTotalDiscountAmountHelper($saleReportEloquent->project_id, 'VARIATIONORDER');

                $data = [
                    'customers_array' => $customerPaymentEloquent->saleReport->project->customersPivot->toArray(),
                    'company_name' => $company->name,
                    'address' => $address,
                    'type' => $customerPaymentEloquent->paymentType->name,
                    'amount' => $customerPaymentEloquent->amount,
                    'customerPaymentInvNo' => $customerPaymentEloquent->invoice_no,
                    'description' => $customerPaymentEloquent->description,
                    'remark' => $customerPaymentEloquent->remark,
                    'payment_date' => $customerPaymentEloquent->created_at->format('d/m/Y'),
                    'netAmountTaxable' => $netAmountTaxable,
                    'gst' => $gst,
                    'gst_inclusive' => $isGstInclude,
                    'agreementNo' => $agreementNo,
                    'contractPrice' => $contractPrice,
                    'status' => $customerPaymentEloquent->status,
                    'salepersons' => $salepersonLists,
                    'company_name' => $company->name,
                    'total_sales_amount' => $total_sales_amount,
                    'paid_amount' => $paid_amount,
                    'remaining_amount' => $remaining_amount,
                    'sortQuotation' => $sortData,
                    'sortVariation' => $voSortData,
                    'qoDiscountAmount' => $qoDiscountAmount,
                    'voDiscountAmount' => $voDiscountAmount
                ];

                $headerFooterData = [
                    'companies' => $companies,
                    'company_name' => $company->name,
                    'company_email' => $company->email,
                    'company_address' => $company->main_office,
                    'company_tel' => $company->tel,
                    'company_fax' => $company->fax,
                    'company_reg_no' => $company->gst_reg_no ? $company->gst_reg_no : $company->reg_no,
                    'properties' => $fullAddress,
                    'customers_array' => $customerPaymentEloquent->saleReport->project->customersPivot->toArray(),
                    'enable_show_last_name_first' => $enable_show_last_name_first->value ?? null,
                    'payment_date' => $customerPaymentEloquent->created_at->format('d/m/Y'),
                    'created_at' => $this->convertDate($customerPaymentEloquent->created_at->format('d/m/Y')),
                    'document_agreement_no' => $agreementNo,
                    'customerPaymentInvNo' => $customerPaymentEloquent->invoice_no,
                    'salepersons' => $salepersonLists,
                    'footer_text' => $company->document_standards?->footer_text
                ];

            if ($folder_name === 'Tidplus' || $folder_name === 'Whst' || $folder_name === 'Tag' || $folder_name === 'Henglai' || $folder_name === 'Molecule' || $folder_name === 'Metis' || $folder_name === 'Ideology') {

                // if ($customerPaymentEloquent->status == 1 && !is_null($customerPaymentEloquent->unpaid_invoice_file_path)) {
                if ($customerPaymentEloquent->status == 1) {
                    $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                    $paidFilePath = $this->downloadInvoicePdf($folder_name, '.paid_invoice', $data, $headerFooterData);
                    $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                        'paid_invoice_file_path' => $paidFilePath,
                        'unpaid_invoice_file_path' => $unpaidFilePath
                    ]);
                } else {
                    $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                    $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                        'unpaid_invoice_file_path' => $unpaidFilePath
                    ]);
                }

                // if ($finalCustomerPayment->status != 1 && $finalCustomerPayment->id != $customerPaymentEloquent->id) {
                //     $this->regenerateLastPdf($folder_name, '.invoice', $finalCustomerPayment, $headerFooterData);
                // }
            } else {
                $folder_name = 'Molecule';
                if ($customerPaymentEloquent->status == 1) {
                    $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                    $paidFilePath = $this->downloadInvoicePdf($folder_name, '.paid_invoice', $data, $headerFooterData);
                    $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                        'paid_invoice_file_path' => $paidFilePath,
                        'unpaid_invoice_file_path' => $unpaidFilePath
                    ]);
                } else {
                    $unpaidFilePath = $this->downloadInvoicePdf($folder_name, '.invoice', $data, $headerFooterData);
                    $customerPayment = CustomerPaymentEloquentModel::find($customerPaymentEloquent->id)->update([
                        'unpaid_invoice_file_path' => $unpaidFilePath
                    ]);
                }
            }
        }



        return [
            'status' => true,
            'data' => CustomerPaymentMapper::fromEloquent($customerPaymentEloquent)
        ];
    }

    public function refundPayment(array $data, int $customerPaymentId)
    {
        $customerPaymentEloquent = CustomerPaymentEloquentModel::findOrFail($customerPaymentId);

        $saleReportEloquent = SaleReportEloquentModel::where('id', $customerPaymentEloquent->sale_report_id)->first();

        if($customerPaymentEloquent->status === 0){
            return [
                'status' => false,
                'data' => 'This payment is not paid yet.'
            ];
        }elseif($customerPaymentEloquent->is_refunded === 1){

            return [
                'status' => false,
                'data' => 'This payment is already refunded.'
            ];

        }elseif ($customerPaymentEloquent->amount < $data['amount']) {

            return [
                'status' => false,
                'data' => 'Refund amount exceeds the payment amount.'
            ];
        }

        $saleReportEloquent->update([
            'paid' => $saleReportEloquent->paid - $data['amount'],
            'remaining' => $saleReportEloquent->remaining + $data['amount']
        ]);

        $customerPaymentEloquent->update([
            'is_refunded' => 1,
            'refund_amount' => $data['amount'],
            'refund_date' => $data['refund_date'],
            'remark' => $data['remark']
        ]);

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            if($generalSettingEloquent->value == 'quickbooks'){
                $qboCustomerId = $saleReportEloquent->project->customer->quick_book_user_id;
            }else{
                $qboCustomerId = $saleReportEloquent->project->customer->xero_user_id;
            }

            try {

                if(is_null($saleReportEloquent->project->company->gst_reg_no) || $saleReportEloquent->project->company->gst_reg_no === ''){
                    $isGstEnable = false;
                }else{
                    $isGstEnable = true;
                }

                $taxInclusiveAmt = $data['amount'];

                $netAmountTaxable = $isGstEnable ? round($taxInclusiveAmt / 1.09, 2) : $taxInclusiveAmt ;

                $totalTax = $isGstEnable ? round($taxInclusiveAmt - $netAmountTaxable, 2) : 0;

                $accountingSettings = AccountingSettingEloquentModel::where('company_id',$saleReportEloquent->project->company_id)->get();

                $taxCodeRef = optional($accountingSettings->where('setting', 'taxCodeOfInvoice')->first())->value;
                $taxRateRef = optional($accountingSettings->where('setting', 'taxRateOfInvoice')->first())->value;
                $itemRef =  optional($accountingSettings->where('setting', 'RefundService')->first())->value;

                $creditNoteData = [
                    'netAmountTaxable' => $netAmountTaxable,
                    'taxInclusiveAmt' => $taxInclusiveAmt,
                    'totalTax' => $totalTax,
                    'taxCodeRef' => $taxCodeRef,
                    'taxRateRef' => $taxRateRef,
                    'TaxPercent' => $isGstEnable ? 9 : 0,
                    'customerId' => $qboCustomerId,
                    'itemRef' => $itemRef,
                    'name' => $customerPaymentEloquent->description,
                    'remark' => $data['remark'],
                    'classRef' => $saleReportEloquent->project->quickbook_class_id,
                    'isGstEnable' => $isGstEnable,
                    'invoiceDate' => $data['refund_date'],
                ];

                Log::info("Stoe Credit Note Data: " . json_encode($creditNoteData));

                $creditNote = $this->accountingService->storeCreditMemo($saleReportEloquent->project->company_id,$creditNoteData);

                if($generalSettingEloquent->value == 'quickbooks'){

                    $qboCreditNoteFilePath = $this->accountingService->saveCreditNotePdf($saleReportEloquent->project->company_id,$creditNote->Id);

                    $customerPaymentEloquent->update([
                        'quick_book_credit_note_id' => $creditNote->Id,
                        'credit_note_file_path' => $qboCreditNoteFilePath
                    ]);

                }

            } catch (\Exception $e) {

                Log::info($e->getMessage());
            }
        }

        return [
            'status' => true,
            'data' => 'Successfully reund the customer payment.'
        ];
    }

    public function destroy(int $customer_payment_id) : void {

        $customerPaymentEloquent = CustomerPaymentEloquentModel::query()->findOrFail($customer_payment_id);

        if($customerPaymentEloquent->status === 1){

            $saleReportEloquent = SaleReportEloquentModel::where('id', $customerPaymentEloquent->sale_report_id)->first();

            $saleReportEloquent->update([
                'paid' => $saleReportEloquent->paid - $customerPaymentEloquent->amount,
                'remaining' => $saleReportEloquent->remaining + $customerPaymentEloquent->amount
            ]);

        }

        $customerPaymentEloquent->delete();
    }

    public function getProjectsForExport()
    {
        return CustomerPaymentEloquentModel::whereNull('deleted_at')
        ->orderBy('sale_report_id', 'asc')->get();
    }

    private function getCompanyLogo($company_logo)
    {
        if ($company_logo) {
            $customer_file_path = 'logo/' . $company_logo;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
            return $company_base64Image;
        }
    }

    public function updateEstimatedDate($customer_payments)
    {
        DB::beginTransaction();
        try {
            $decoded_customer_payments = $customer_payments ? json_decode($customer_payments) : null;
            if($decoded_customer_payments && !empty($decoded_customer_payments)){
                foreach ($decoded_customer_payments as $decoded_customer_payment) {
                    $customer_payment = CustomerPaymentEloquentModel::find($decoded_customer_payment->id);
                    if($customer_payment){
                        $customer_payment->update([
                            'estimated_date' => $decoded_customer_payment->estimated_date
                        ]);
                    }
                }
            }
            DB::commit();
            return $decoded_customer_payments;
        } catch (\Exception $error) {
            DB::rollBack();
            throw new \Exception($error->getMessage());
        }
    }

    public function downloadInvoicePdf($folder_name, $pdfFile, $data, $headerFooterData)
    {
        Log::channel('daily')->info($data);
        Log::channel('daily')->info($headerFooterData);

        $pdf = \PDF::loadView('pdf.CUSTOMER_INVOICE.' . $folder_name . $pdfFile, $data);
        $pdf->setOption('enable-javascript', true);

        if($folder_name === 'Tag'){
            $pdf->setOption('margin-top', 60);
            $pdf->setOption('margin-left', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 0);

            $headerHtml = view('pdf.Common.Tag.customerInvoiceHeader', $headerFooterData)->render();
            $footerHtml = view('pdf.Common.Tag.footer', $headerFooterData)->render();
        } else if($folder_name === 'Molecule'){
            $pdf->setOption('margin-top', 40);
            $pdf->setOption('margin-left', 0);
            $pdf->setOption('margin-right', 0);
            $pdf->setOption('margin-bottom', 20);

            $headerHtml = view('pdf.Common.Molecule.customerInvoiceHeader', $headerFooterData)->render();
            $footerHtml = view('pdf.Common.Molecule.customerInvoiceFooter', $headerFooterData)->render();
        } else if($folder_name === 'Metis'){
            $pdf->setOption('margin-top', 60);
            $pdf->setOption('margin-left', 5);
            $pdf->setOption('margin-right', 5);
            $pdf->setOption('margin-bottom', 0);

            $headerHtml = view('pdf.Common.Metis.customerInvoiceHeader', $headerFooterData)->render();
            $footerHtml = view('pdf.Common.Metis.customerInvoiceFooter', $headerFooterData)->render();
        } else if($folder_name === 'Henglai') {
            $pdf->setOption('margin-top', 80);
            $pdf->setOption('margin-left', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 0);
            $headerHtml = view('pdf.Common.Henglai.customerInvoiceHeader', $headerFooterData)->render();
            $footerHtml = view('pdf.Common.Tag.footer', $headerFooterData)->render();
        } else if($folder_name === 'Ideology') {
            $pdf->setOption('margin-top', 130);
            $pdf->setOption('margin-left', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 0);
            $headerHtml = view('pdf.Common.Ideology.customerInvoiceHeader', $headerFooterData)->render();
            $footerHtml = view('pdf.Common.Tag.footer', $headerFooterData)->render();
        } else {
            $pdf->setOption('margin-top', 40);
            $pdf->setOption('margin-left', 0);
            $pdf->setOption('margin-right', 0);
            $pdf->setOption('margin-bottom', 15);

            $headerHtml = view('pdf.Common.Tidplus.header', $headerFooterData)->render();
            $footerHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();
        }

        $pdf->setOption('footer-html', $footerHtml);
        $pdf->setOption('header-html', $headerHtml);
        $fileName = 'paid_customer_invoice' . uniqid() . '.pdf';
        // Save the PDF to a file in the 'public' disk
        $filePath = 'pdfs/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());
        return $filePath;
    }

    public function regenerateLastPdf($folder_name, $pdfFile, $finalCustomerPayment, $headerFooterData)
    {
        if($finalCustomerPayment->saleReport->project->company->gst !== 0){

            $netAmountTaxable = round($finalCustomerPayment->amount / 1.09, 2);

            $gst = round($finalCustomerPayment->amount - $netAmountTaxable, 2);
            $isGstInclude = true;
        }else{
            $netAmountTaxable = $finalCustomerPayment->amount;
            $gst = 0;
            $isGstInclude = false;
        }

        $blockNum =  $finalCustomerPayment->saleReport->project->property->block_num ?? null;
        $streetName = $finalCustomerPayment->saleReport->project->property->street_name ?? null;
        $unitNum = $finalCustomerPayment->saleReport->project->property->unit_num ?? null;
        $agreementNo = $finalCustomerPayment->saleReport->project->agreement_no ?? null;
        $company = $finalCustomerPayment->saleReport->project->company ?? null;
        $contractPrice = $finalCustomerPayment->saleReport->total_sales ?? null;

        $address = $blockNum . ' ' . $streetName . ' ' . $unitNum;
        $agreementNo = $finalCustomerPayment->saleReport->project->agreement_no ?? null;
        $contractPrice = $finalCustomerPayment->saleReport->total_sales ?? null;
        $total_sales_amount = $finalCustomerPayment->saleReport->total_sales;
        $paid_amount = $finalCustomerPayment->saleReport->paid;
        $remaining_amount = $finalCustomerPayment->saleReport->remaining;
        $sortData = generateContractItems($finalCustomerPayment?->saleReport?->project_id);
        $voSortData = generateVOItems($finalCustomerPayment?->saleReport?->project_id);

        $data = [
            'customers_array' => $finalCustomerPayment->saleReport->project->customersPivot->toArray(),
            'address' => $address,
            'type' => $finalCustomerPayment->paymentType->name,
            'amount' => $finalCustomerPayment->amount,
            'customerPaymentInvNo' => $finalCustomerPayment->invoice_no,
            'description' => $finalCustomerPayment->description,
            'remark' => $finalCustomerPayment->remark,
            'payment_date' => $finalCustomerPayment->created_at->format('d/m/Y'),
            'netAmountTaxable' => $netAmountTaxable,
            'gst' => $gst,
            'gst_inclusive' => $isGstInclude,
            'agreementNo' => $agreementNo,
            'contractPrice' => $contractPrice,
            'status' => $finalCustomerPayment->status,
            'company_name' => $company->name,
            'total_sales_amount' => $total_sales_amount,
            'paid_amount' => $paid_amount,
            'remaining_amount' => $remaining_amount,
            'sortQuotation' => $sortData,
            'sortVariation' => $voSortData

        ];
        $finalPath = $this->downloadInvoicePdf($folder_name, $pdfFile, $data, $headerFooterData);
        $finalCustomerPayment->update([
            'unpaid_invoice_file_path' => $finalPath
        ]);
        return $finalPath;
    }

    function convertDate($dateString)
    {
        $originalFormat = 'd/m/Y';
        $date = DateTime::createFromFormat($originalFormat, $dateString);
        if ($date) {
            $formattedDate = $date->format('j M Y');
            return $formattedDate;
        } else {
            return '';
        }
    }
}
