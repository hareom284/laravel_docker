<?php

namespace Src\Company\Document\Domain\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Domain\Mail\NotifyExceedSupplierCostingAmountMail;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\VendorInvoiceExpenseTypeEloquentModel;

class VendorInvoicesImport implements ToCollection
{
    protected $sheetName;

    public function __construct($sheetName)
    {
        $this->sheetName = $sheetName;
    }
    public function collection(Collection $rows)
    {
        $toArray = $rows->toArray();
        array_splice($toArray, 0, 1);
        $toCollection = collect($toArray);

        foreach ($toCollection as $row) 
        {
            $project = ProjectEloquentModel::where('agreement_no', $row[0])->first();

            $excelDate = $row[4];
        
            if (is_numeric($excelDate)) {
                $phpDate = Date::excelToDateTimeObject($excelDate)->format('Y-m-d');
            } else {
                // Handle already formatted dates
                $phpDate = \Carbon\Carbon::parse($excelDate)->format('Y-m-d');
            }

            $vendor = VendorEloquentModel::where('vendor_name', $row[2])->first();
            if (!$vendor) {
                throw new \Exception("Vendor '{$row[2]}' not found.");
            }

            $expenseType = VendorInvoiceExpenseTypeEloquentModel::where('name', $row[1])->first();
            if (!$expenseType) {
                throw new \Exception("Expense Type '{$row[1]}' not found.");
            }

            $generalSettingEloquent = GeneralSettingEloquentModel::where('setting','enable_approive_supplier_costing')->first();
            $status = 0;
            if($generalSettingEloquent && $generalSettingEloquent->value == 'false'){
                $status = 2;
            }
            if(is_null($project))
            {
                $status = 2;
            }
            Log::info('invoice date formatted '. $phpDate);
            Log::info('invoice date '. $phpDate);
            $supplierCosting = SupplierCostingEloquentModel::create([
                'invoice_no' => $row[3],
                'invoice_date' => $phpDate,
                'description' => $row[5],
                'payment_amt' => $row[6],
                'amount_paid' => $row[7],
                'to_pay' => $row[8],
                'discount_percentage' => $row[9],
                'discount_amt' => $row[10],
                'is_gst_inclusive' => $row[11],
                'gst_value' => $row[12],
                'document_file' => $row[13] ?? null,
                'status' => $status,
                'project_id' => $project?->id,
                'vendor_id' => $vendor->id,
                'vendor_invoice_expense_type_id' => $expenseType->id,
            ]);

            // if(!is_null($supplierCosting->project_id))
            // {
            //     $this->sendNotifyMail($supplierCosting);
            // }

            // $qboConfig = config('quickbooks');
            // if($qboConfig['qbo_integration']){
            //     $this->processQBO($supplierCosting);
            // }
        }        
    }

    private function sendNotifyMail($supplierCosting)
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

    private function processQBO($supplierCosting)
    {
        $quickBookService = new QuickbookService();
        $existsQboVendorId = $supplierCosting->vendor->quick_book_vendor_id;
        $existsQboCustomerId = $supplierCosting->project->customer->quick_book_user_id;

        if(is_null($existsQboCustomerId)){

            $customerName = $supplierCosting->project->customer->first_name . ' ' . $supplierCosting->project->customer->last_name;

            $type = $supplierCosting->project->customer->customers->customer_type ? 1 : 0;

            $quickBookCustomer = $quickBookService->getCusomter($customerName);

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

                $qboRecentCusomter = $quickBookService->saveOrGetQuickbookCustomer($customerData);

                $qboCustomerId = $qboRecentCusomter->Id;

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

        if(is_null($existsQboVendorId)){

            $vendorName = $supplierCosting->vendor->vendor_name;

            $quickBookVendor = $quickBookService->getVendorByName($vendorName);

            if(!$quickBookVendor){

                $vendorData = [
                    'name' => $supplierCosting->vendor->vendor_name,
                    'contact_person' => $supplierCosting->vendor->contact_person,
                    'contact_no' => $supplierCosting->vendor->contact_person_number,
                    'email' => $supplierCosting->vendor->email,
                    'street_name' => $supplierCosting->vendor->street_name,
                    'postal_code' => $supplierCosting->vendor->postal_code,
                ];

                $qboRecentVendor = $quickBookService->storeVendor($vendorData);

                $qboVendorId = $qboRecentVendor->Id;

                VendorEloquentModel::find($supplierCosting->vendor->id)->update([
                    'quick_book_vendor_id' => $qboVendorId
                ]);

            }else{

                $qboVendorId = $quickBookVendor->Id;

                VendorEloquentModel::find($supplierCosting->vendor->id)->update([
                    'quick_book_vendor_id' => $qboVendorId
                ]);
            }

        }else{

            $qboVendorId = $existsQboVendorId;
        }

        try {

            $taxCodeRef = $supplierCosting->is_gst_inclusive == 1 ? 58 : 21;
            $taxRateRef = $supplierCosting->is_gst_inclusive == 1 ? 63 : 18;
            $taxPercent = $supplierCosting->is_gst_inclusive == 1 ? 9 : 0;
            $globalTaxCalculation = $supplierCosting->is_gst_inclusive == 1 ? 'TaxExcluded' : 'TaxInclusive';

            $totalAmount = $supplierCosting->payment_amt;
            $gstAmount = $supplierCosting->gst_value;
            $amount = $supplierCosting->is_gst_inclusive == 1 ? $totalAmount - $gstAmount : $totalAmount;

            $billData = [
                'vendorID' => $qboVendorId,
                'userID' => $qboCustomerId,
                'amount' => $amount,
                'totalAmount' => $totalAmount,
                'totalTax' => $gstAmount,
                'invoiceDate' => $supplierCosting->invoice_date,
                'description' => $supplierCosting->description,
                'invoiceNo' => $supplierCosting->invoice_no,
                'PrivateNote' => $supplierCosting->description,
                'taxCodeRef' => $taxCodeRef,
                'taxRateRef' => $taxRateRef,
                'taxPercent' => $taxPercent,
                'globalTaxCalculation' => $globalTaxCalculation,
                'quickBookExpenseID' => $supplierCosting->quick_book_expense_id
            ];

            $quickBookBill = $quickBookService->storeBill($billData);

            SupplierCostingEloquentModel::find($supplierCosting->id)->update([
                'quick_book_bill_id' => $quickBookBill->Id,
            ]);

        } catch (\Exception $e) {

            Log::debug($e);
        }
    }
}