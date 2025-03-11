<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\Double;
use Src\Company\Project\Infrastructure\EloquentModels\CustomerPaymentEloquentModel;

class CustomerPaymentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $payment_type,
        public readonly ?string $invoice_no,
        public readonly ?string $description,
        public readonly int $index,
        public readonly float $amount,
        public readonly ?string $remark,
        public readonly int $status,
        public readonly int $sale_report_id
    )
    {}

    public static function fromRequest(Request $request, ?int $customer_payment_id = null): CustomerPaymentData
    {
        return new self(
            id: $customer_payment_id,
            payment_type: $request->integer('payment_type'),
            invoice_no: $request->string('invoice_no'),
            description: $request->string('description'),
            index: $request->integer('index'),
            amount: $request->double('amount'),
            remark: $request->string('remark'),
            status: $request->int('sstatus'),
            sale_report_id: $request->integer('sale_report_id')
        );
    }

    public static function fromEloquent(CustomerPaymentEloquentModel $customerPaymentEloquent): self
    {
        return new self(
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'payment_type' => $this->payment_type,
            'invoice_no' => $this->invoice_no,
            'description' => $this->description,
            'index' => $this->index,
            'amount' => $this->amount,
            'remark' => $this->remark,
            'status' => $this->status,
            'sale_report_id' => $this->sale_report_id
        ];
    }
}