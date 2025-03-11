<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class SupplierCostingData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $invoice_no,
        public readonly ?string $description,
        public readonly ?float $payment_amt,
        public readonly ?float $amount_paid,
        public readonly ?float $amended_amt,
        public readonly ?string $remark,
        public readonly ?float $to_pay,
        public readonly ?float $discount_percentage,
        public readonly ?float $discount_amt,
        public readonly ?float $credit_amt,
        public readonly ?string $invoice_date,
        public readonly ?int $is_gst_inclusive,
        public readonly ?float $gst_value,
        public readonly ?string $document_file,
        public readonly ?int $project_id,
        public readonly int $vendor_id,
        public readonly int $status,
        public readonly ?int $purchase_order_id,
        public readonly ?int $quick_book_expense_id, 
        public readonly ?int $vendor_invoice_expense_type_id  
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): SupplierCostingData
    {
        return new self(
            id: $id,
            invoice_no: $request->string('invoice_no'),
            description: $request->string('description'),
            payment_amt: $request->float('payment_amt'),
            amended_amt: $request->float('amended_amt') ?? null,
            remark: $request->string('remark') ?? null,
            amount_paid: $request->float('amount_paid') ?? null,
            to_pay: $request->float('to_pay') ?? null,
            discount_percentage: $request->float('discount_percentage'),
            discount_amt: $request->float('discount_amt'),
            credit_amt: $request->float('credit_amt') ?? null,
            is_gst_inclusive: $request->integer('is_gst_inclusive') ?? null,
            gst_value: $request->integer('gst_value') ?? null,
            invoice_date: $request->string('invoice_date') ?? null,
            document_file: $request->string('document_file'),
            status: $request->integer('status'),
            project_id: $request->integer('project_id'),
            vendor_id: $request->integer('vendor_id'),
            purchase_order_id: $request->integer('purchase_order_id') ?? null,
            quick_book_expense_id: $request->integer('quick_book_expense_id') ?? null,
            vendor_invoice_expense_type_id: $request->integer('vendor_invoice_expense_type_id') ?? null
        );
    }

    public static function fromEloquent(SupplierCostingEloquentModel $supplierCostingEloquentModel): self
    {
        return new self(
            id: $supplierCostingEloquentModel->id,
            invoice_no: $supplierCostingEloquentModel->invoice_no,
            description: $supplierCostingEloquentModel->description,
            payment_amt: $supplierCostingEloquentModel->payment_amt,
            amended_amt: $supplierCostingEloquentModel->amended_amt,
            remark: $supplierCostingEloquentModel->remark,
            amount_paid: $supplierCostingEloquentModel->amount_paid,
            to_pay: $supplierCostingEloquentModel->to_pay,
            discount_percentage: $supplierCostingEloquentModel->discount_percentage,
            discount_amt: $supplierCostingEloquentModel->discount_amt,
            credit_amt: $supplierCostingEloquentModel->credit_amt,
            is_gst_inclusive: $supplierCostingEloquentModel->is_gst_inclusive,
            gst_value: $supplierCostingEloquentModel->gst_value,
            invoice_date: $supplierCostingEloquentModel->invoice_date,
            document_file: $supplierCostingEloquentModel->document_file,
            status: $supplierCostingEloquentModel->status,
            project_id: $supplierCostingEloquentModel->project_id,
            vendor_id: $supplierCostingEloquentModel->vendor_id,
            purchase_order_id: $supplierCostingEloquentModel->purchase_order_id,
            quick_book_expense_id: $supplierCostingEloquentModel->quick_book_expense_id,
            vendor_invoice_expense_type_id: $supplierCostingEloquentModel->vendor_invoice_expense_type_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'description' => $this->description,
            'payment_amt' => $this->payment_amt,
            'amount_paid' => $this->amount_paid,
            'to_pay' => $this->to_pay,
            'discount_percentage' => $this->discount_percentage,
            'discount_amt' => $this->discount_amt,
            'credit_amt' => $this->credit_amt,
            'is_gst_inclusive' => $this->is_gst_inclusive,
            'gst_vaue' => $this->gst_value,
            'invoice_date' => $this->invoice_date,
            'document_file' => $this->document_file,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'vendor_id' => $this->vendor_id,
            'purchase_order_id' => $this->purchase_order_id,
            'quick_book_expense_id' => $this->quick_book_expense_id,
            'vendor_invoice_expense_type_id' => $this->vendor_invoice_expense_type_id
        ];
    }
}