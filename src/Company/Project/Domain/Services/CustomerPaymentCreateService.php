<?php

namespace Src\Company\Project\Domain\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class CustomerPaymentCreateService
{
    public function storePayment($project_id, $customer_id, $payment_terms)
    {
        $payment_terms_decode = json_decode($payment_terms) ?? null;

        if($payment_terms_decode){
            $customerInvoiceRunningNumberSetting = GeneralSettingEloquentModel::where('setting','enable_customer_invoice_running_number')->first()->value ?? null;
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $currentDate = Carbon::now();

            $generalSetting = GeneralSettingEloquentModel::where('setting', 'customer_invoice_running_number_values')->first();
            $initialcustomerInvoiceRunningNumberValue = $generalSetting->value ?? null;
            $customerInvoiceRunningNumberValue = isset($initialcustomerInvoiceRunningNumberValue)
            ? explode(',', $initialcustomerInvoiceRunningNumberValue)
            : [];
            $project = ProjectEloquentModel::find($project_id);
            $company = $project->company;
            $enableCustomerRunningNumberByMonth = $company->enable_customer_running_number_by_month;
            $saleReport = SaleReportEloquentModel::where('project_id',$project_id)->first();
            $prjInvoiceNo = $project->invoice_no;

            $companies = [
                "company_logo" => $this->getCompanyLogo($project->company->logo),
            ];

            $totalAmount = 0;
            $company = $project->company;
            $invoice_prefix = $company?->invoice_prefix ?? 'INV';

            foreach ($payment_terms_decode->payment_terms as $key => $payment_term) {

                if($customerInvoiceRunningNumberSetting == 'true'){
                    $currentMonthInvoiceRunningNumber = (int)$customerInvoiceRunningNumberValue[$currentMonth - 1] + 1;
                    $customerInvoiceRunningNumberValue[$currentMonth - 1] = $currentMonthInvoiceRunningNumber;

                    $invoiceNumber = $invoice_prefix . $currentYear . $currentDate->format('m') . str_pad($currentMonthInvoiceRunningNumber, 2, '0', STR_PAD_LEFT);

                    $generalSetting->update(['value' => implode(',', $customerInvoiceRunningNumberValue)]);

                    $customerPaymentInvNo = $invoiceNumber;
                }else{
                    if ($enableCustomerRunningNumberByMonth) {
                        $initialcustomerInvoiceRunningNumberValue = $company->customer_invoice_running_number_values;
                        $customerInvoiceRunningNumberValue = isset($initialcustomerInvoiceRunningNumberValue)
                        ? explode(',', $initialcustomerInvoiceRunningNumberValue)
                        : [];
                        $currentMonthInvoiceRunningNumber = (int)$customerInvoiceRunningNumberValue[$currentMonth - 1] + 1;
                        $customerInvoiceRunningNumberValue[$currentMonth - 1] = $currentMonthInvoiceRunningNumber;
            
                        $invoiceNumber = $invoice_prefix . $currentYear . $currentDate->format('m') . str_pad($currentMonthInvoiceRunningNumber, 2, '0', STR_PAD_LEFT);
                        $company->update([
                            'customer_invoice_running_number_values' => implode(',', $customerInvoiceRunningNumberValue)
                        ]);
                        $customerPaymentInvNo = $invoiceNumber;
                    }else{
                        $paddedInvoiceNo = str_pad($prjInvoiceNo, 4, '0', STR_PAD_LEFT);
                        $customerPaymentInvNo = $invoice_prefix . $paddedInvoiceNo . '.' . $key + 1;
                    }
                }

                $customerPayment = CustomerPaymentEloquentModel::create([
                    'customer_id' => $customer_id,
                    'sale_report_id' => $saleReport->id,
                    'invoice_no' => $customerPaymentInvNo,
                    'amount' => $payment_term->amount_payable,
                    'payment_type' => $payment_term->payment_type_id,
                    'index' => $key + 1,
                    'estimated_date' => $payment_term->estimated_date ? $this->convertDate($payment_term->estimated_date) : null
                ]);

                $totalAmount += $payment_term->amount_payable;

                $current_folder_name = config('folder.company_folder_name');
                $folder_name  = $current_folder_name;

                if($project->company->gst !== 0){
                    $netAmountTaxable = round($customerPayment->amount / 1.09, 2);
                    $gst = round($customerPayment->amount - $netAmountTaxable, 2);
                    $isGstInclude = true;
                }else{
                    $netAmountTaxable = $customerPayment->amount;
                    $gst = 0;
                    $isGstInclude = false;
                }

                $data = [
                    'customers_array' => $project->customersPivot->toArray(),
                    'address' => $project->property->block_num . ' ' . $project->property->street_name . ' ' . $project->property->unit_num,
                    'type' => $customerPayment->paymentType->name,
                    'amount' => $customerPayment->amount,
                    'customerPaymentInvNo' => $customerPaymentInvNo,
                    'payment_date' => $customerPayment->created_at->format('d/m/Y'),
                    'description' => $customerPayment->description,
                    'netAmountTaxable' => $netAmountTaxable,
                    'gst' => $gst,
                    'gst_inclusive' => $isGstInclude,
                ];

                $headerFooterData = [
                    'companies' => $companies,
                ];
                
                if($folder_name === 'Tidplus'){

                    $pdf = \PDF::loadView('pdf.CUSTOMER_INVOICE.' . $folder_name . '.invoice', $data);
                    $pdf->setOption('enable-javascript', true);
                    $pdf->setOption('margin-top', 40);
                    $pdf->setOption('margin-bottom', 15);
                    $pdf->setOption('margin-left', 0);
                    $pdf->setOption('margin-right', 0);
                    $headerTidplusHtml = view('pdf.Common.Tidplus.header', $headerFooterData)->render();
                    $footerTidplusHtml = view('pdf.Common.Tidplus.footer', $headerFooterData)->render();

                    $pdf->setOption('footer-html', $footerTidplusHtml);
                    $pdf->setOption('header-html', $headerTidplusHtml);

                    $fileName = 'customer_invoice' . time() . '.pdf';
                    // Save the PDF to a file in the 'public' disk
                    $filePath = 'pdfs/' . $fileName;
                    Storage::disk('public')->put($filePath, $pdf->output());

                    CustomerPaymentEloquentModel::find($customerPayment->id)->update([
                        'unpaid_invoice_file_path' => $filePath,
                    ]);
                }
            }


            // $saleReport->update([
            //     'paid' => $totalAmount,
            //     'remaining' => $saleReport->remaining - $totalAmount,
            // ]);
        }
    }

    public function convertDate($date)
    {
        return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
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
}
