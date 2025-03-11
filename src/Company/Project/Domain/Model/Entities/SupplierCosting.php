<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class SupplierCosting extends AggregateRoot implements \JsonSerializable
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
        public readonly int $status,
        public readonly ?int $project_id,
        public readonly int $vendor_id,
        public readonly ?int $purchase_order_id,
        public readonly ?int $quick_book_expense_id,
        public readonly ?int $vendor_invoice_expense_type_id
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'description' => $this->description,
            'payment_amt' => $this->payment_amt,
            'amount_paid' => $this->amount_paid,
            'amended_amt' => $this->amended_amt,
            'remark' => $this->remark,
            'to_pay' => $this->to_pay,
            'discount_percentage' => $this->discount_percentage,
            'discount_amt' => $this->discount_amt,
            'credit_amt' => $this->credit_amt,
            'is_gst_inclusive' => $this->is_gst_inclusive,
            'gst_value' => $this->gst_value,
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}