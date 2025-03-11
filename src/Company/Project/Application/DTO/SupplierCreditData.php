<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;

class SupplierCreditData
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

    public static function fromRequest(Request $request, ?int $id = null): SupplierCreditData
    {
        return new self(
            id: $id,
            invoice_no: $request->string('invoice_no'),
            description: $request->float('description'),
            is_gst_inclusive: $request->integer('is_gst_inclusive'),
            amount: $request->float('amount'),
            total_amount: $request->float('total_amount'),
            gst_amount: $request->string('gst_amount') ?? null,
            invoice_date: $request->string('invoice_date'),
            pdf_path: $request->string('pdf_path') ?? null,
            quick_book_vendor_credit_id: $request->integer('quick_book_vendor_credit_id') ?? null,
            vendor_id: $request->integer('vendor_id'),
            sale_report_id: $request->integer('sale_report_id') ?? null,
        );
    }

    public static function fromEloquent(SupplierCreditEloquentModel $supplierCreditEloquentModel): self
    {
        return new self(
            id: $supplierCreditEloquentModel->id,
            invoice_no: $supplierCreditEloquentModel->invoice_no,
            description: $supplierCreditEloquentModel->description,
            is_gst_inclusive: $supplierCreditEloquentModel->is_gst_inclusive,
            amount: $supplierCreditEloquentModel->amount,
            total_amount: $supplierCreditEloquentModel->total_amount,
            gst_amount: $supplierCreditEloquentModel->gst_amount,
            invoice_date: $supplierCreditEloquentModel->invoice_date,
            pdf_path: $supplierCreditEloquentModel->pdf_path,
            quick_book_vendor_credit_id: $supplierCreditEloquentModel->quick_book_vendor_credit_id,
            vendor_id: $supplierCreditEloquentModel->vendor_id,
            sale_report_id: $supplierCreditEloquentModel->sale_report_id,
        );
    }

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'invoice_no' => $this->invoice_no,
           'description' => $this->description,
           'is_gst_inclusive' => $this->is_gst_inclusive,
           'amount' => $this->amount,
           'total_amount' => $this->total_amount,
           'gst_amount' => $this->gst_amount,
           'invoice_date' => $this->invoice_date,
           'pdf_path' => $this->pdf_path,
           'quick_book_vendor_credit_id' => $this->quick_book_vendor_credit_id,
           'vendor_id' => $this->vendor_id,
           'sale_report_id' => $this->sale_report_id,
        ];
    }
}