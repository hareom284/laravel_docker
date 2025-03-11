<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;

class UpdateCustomerPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customerPayments = CustomerPaymentEloquentModel::where('sale_report_id', '!=', 0)
                            ->get()
                            ->groupBy('sale_report_id');
        
        foreach ($customerPayments as $saleReportId => $customerPayment) {

            $totalAmount = 0;

            foreach ($customerPayment as $payment) {
                
                $totalAmount += $payment->amount;

                $payment->status = 2;

                if(is_null($payment->paid_invoice_file_path)) {
                    $payment->paid_invoice_file_path = $payment->unpaid_invoice_file_path;

                    $payment->unpaid_invoice_file_path = null;
                }

                $payment->save();
            }

            $saleReport = $customerPayment->first()->saleReport;

            $saleReport->update([
                'paid' => $totalAmount,
                'remaining' => $saleReport->total_sales - $totalAmount
            ]);
        }
    }
}
