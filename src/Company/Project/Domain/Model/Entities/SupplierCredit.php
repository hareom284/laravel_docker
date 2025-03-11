<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class SupplierCredit extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $invoice_no,
        public readonly string $description,
        public readonly int $is_gst_inclusive,
        public readonly float $total_amount,
        public readonly float $amount,
        public readonly ?float $gst_amount,
        public readonly string $invoice_date,
        public readonly ?string $pdf_path,
        public readonly ?int $quick_book_vendor_credit_id,
        public readonly int $vendor_id,
        public readonly int $sale_report_id,
    )
    {}

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'invoice_no' => $this->invoice_no,
           'description' => $this->description,
           'is_gst_inclusive' => $this->is_gst_inclusive,
           'total_amount' => $this->total_amount,
           'amount' => $this->amount,
           'gst_amount' => $this->gst_amount,
           'invoice_date' => $this->invoice_date,
           'pdf_path' => $this->pdf_path,
           'quick_book_vendor_credit_id' => $this->quick_book_vendor_credit_id,
           'vendor_id' => $this->vendor_id,
           'sale_report_id' => $this->sale_report_id
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}