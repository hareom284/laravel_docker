<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\CustomerPayment;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;
use Src\Company\Project\Application\DTO\CustomerPaymentData;
use function PHPSTORM_META\type;
use Carbon\Carbon;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class CustomerPaymentMapper {
    
    public static function fromRequest(Request $request, ?int $customer_payment_id=null): CustomerPayment
    {
        $checkExists = CustomerPaymentEloquentModel::where('payment_type',$request->payment_type)
                    ->where('sale_report_id',$request->sale_report_id)
                    ->get();

        $index = count($checkExists) + 1;

        $status = $request->status ? 1 : 0 ;

        return new CustomerPayment(
            id: $customer_payment_id,
            payment_type: $request->integer('payment_type'),
            invoice_no: $request->filled('invoice_no') ? $request->input('invoice_no') : null,
            description: $request->filled('description') ? $request->input('description') : null,
            invoice_date: $request->filled('invoice_date') ? $request->input('invoice_date') : null,
            index: $index,
            amount: floatval($request->input('amount')),
            status : $status,
            remark: $request->filled('remark') ? $request->input('remark') : null,
            bank_info: $request->filled('bank_info') ? $request->input('bank_info') : null,
            sale_report_id: $request->integer('sale_report_id')
        );
    }

    public static function fromEloquent(CustomerPaymentEloquentModel $customerPaymentEloquent): CustomerPaymentData
    {
        return new CustomerPaymentData(
            id: $customerPaymentEloquent->id,
            payment_type: $customerPaymentEloquent->payment_type,
            invoice_no: $customerPaymentEloquent->invoice_no,
            description: $customerPaymentEloquent->description,
            index: $customerPaymentEloquent->index,
            amount: $customerPaymentEloquent->amount,
            remark: $customerPaymentEloquent->remark,
            status: $customerPaymentEloquent->status,
            sale_report_id: $customerPaymentEloquent->sale_report_id
        );
    }

    public static function toEloquent(CustomerPayment $customerPayment): CustomerPaymentEloquentModel
    {
        $customerPaymentEloquent = new CustomerPaymentEloquentModel();
        if($customerPayment->id)
        {
            $customerPaymentEloquent = CustomerPaymentEloquentModel::query()->findOrFail($customerPayment->id);
        }

        $customerPaymentEloquent->payment_type = $customerPayment->payment_type;
        $customerPaymentEloquent->invoice_date = $customerPayment->invoice_date;
        $customerPaymentEloquent->description = $customerPayment->description;
        $customerPaymentEloquent->index = $customerPayment->index;
        $customerPaymentEloquent->amount = $customerPayment->amount;
        $customerPaymentEloquent->remark = $customerPayment->remark;
        $customerPaymentEloquent->status = $customerPayment->status;
        $customerPaymentEloquent->sale_report_id = $customerPayment->sale_report_id;
        $customerPaymentEloquent->customer_id = 1;

        return $customerPaymentEloquent;
    }
}