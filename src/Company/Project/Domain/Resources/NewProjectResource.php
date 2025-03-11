<?php

namespace Src\Company\Project\Domain\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingPaymentEloquentModel;
use stdClass;

class NewProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $saleperson = [];

        foreach ($this->salespersons as $value) {
            $arr = new stdClass();
            $arr->name = $value->first_name . " " . $value->last_name;
            $arr->email = $value->email;
            array_push($saleperson, $arr);
        }

        $property = $this->properties;
        $month = null;
        $created_date = null;
        $completed_date = null;
        $started_date = null;
        $date = $this->created_at ? Carbon::parse($this->created_at) : null;
        $project_completed_date = $this->completed_date ? Carbon::parse($this->completed_date) : null;
        $project_started_date = $this->expected_date_of_completion ? Carbon::parse($this->expected_date_of_completion) : null;
        if($date){
            $month = $date->format('M-y');
            $created_date = $date->format('j-M-y');
        }

        if($project_completed_date){
            $completed_date = $date->format('j-M-y');
        }

        if($project_started_date){
            $started_date = $date->format('j-M-y');
        }

        $signedQuotation = $this->renovation_documents->first(function ($document) {
            return $document['type'] === 'QUOTATION' && !empty($document['signed_date']);
        });

        $todaysCustomerPayments = $this->saleReport?->customer_payments()
                                    ->whereDate('created_at', Carbon::today())
                                    ->whereNull('refund_date')
                                    ->sum('amount') ?? 0;

        $todaysSupplierCostings = $this->supplierCostings()->whereDate('invoice_date', Carbon::today())->sum('payment_amt');

        $todaySupplierCredit = $this->saleReport?->supplier_credits()->whereDate('invoice_date', Carbon::today())->sum('amount') ?? 0;

        $todaySupplierCostingPayments = $this->getTodaySupplierCostingPayments($this->supplierCostings);
        
        $quotationAmount = $signedQuotation ? $signedQuotation['total_amount'] : null;
        return [
            'id' => $this->id,
            'name' => $property->block_num.' '.$property->street_name.' #'.$property->unit_num.' Singapore '.$property->postal_code,
            'invoice_no' => $this->invoice_no,
            'status' => $this->project_status,
            'customer_name' => $this->customers->first_name . " " . $this->customers->last_name,
            'customer_email' => $this->customers->email,
            'customer_contact' => $this->customers->contact_no,
            'salepersons' => $saleperson,
            'total_amount' => $this->saleReport ? $this->saleReport->total_sales : 0,
            'paid_amount' => $this->saleReport ? $this->saleReport->paid : 0,
            'remaining_amount' => $this->saleReport ? $this->saleReport->remaining : 0,
            'freezed' => $this->freezed,
            'request_note' => $this->request_note,
            'payment_status' => $this->payment_status,
            'sale_report' => $this->saleReport,
            'company' => $this->company,
            'month' => $month,
            'created_date' => $created_date,
            'started_date' => $started_date,
            'completed_date' => $completed_date,
            'profit_margin' => $this->calculateTotalProfitMarginPercentage($signedQuotation),
            'quotation_amount' => $quotationAmount,
            'customer_payments' => $todaysCustomerPayments,
            'supplier_costings' => $todaysSupplierCostings,
            'supplier_credits' => $todaySupplierCredit,
            'balance' => $this->saleReport ? $this->saleReport->remaining : 0,
            'supplier_costings_payment' => $todaySupplierCostingPayments,
            'balance_supplier_costings' => 0,
            'balance_of_customer_payments_and_supplier_costings' => 0,
        ];
    }

    public function calculateTotalProfitMarginPercentage($signedQuotation)
    {   
        if($signedQuotation){
            $totalCost = 0;
            $totalPrice = 0;
            $sections = $signedQuotation->renovation_sections ? $signedQuotation->renovation_sections : [];
            foreach ($sections as $section) {
                $totalCost += $section['total_cost_price'];
                $totalPrice += $section['total_price'];
            }
    
            if ($totalPrice == 0) {
                return 0;
            }
    
            $profitMargin = (($totalPrice - $totalCost) / $totalPrice) * 100;
    
            return $profitMargin;
        }
        return null;
    }

    public function getTodaySupplierCostingPayments($supplierCostings)
    {
        $result = 0;

        if(count($supplierCostings) > 0){

            SupplierCostingPaymentEloquentModel::whereDate('payment_date', Carbon::today())
            ->whereHas('supplierCostings', function($query) use ($supplierCostings){
                $query->whereIn('vendor_invoice_id', $supplierCostings->pluck('id'));
            })->get()->each(function($payment) use (&$result){
                $result += $payment->amount;
            });

            return $result;
            
        }else{
            return $result;
        }
    }

    
}
