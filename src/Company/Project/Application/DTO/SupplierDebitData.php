<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;

class SupplierDebitData
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
        public readonly int $vendor_id,
        public readonly int $sale_report_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): SupplierDebitData
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
            vendor_id: $request->integer('vendor_id'),
            sale_report_id: $request->integer('sale_report_id') ?? null,
        );
    }

    public static function fromEloquent(SupplierDebitEloquentModel $supplierDebitEloquentModel): self
    {
        return new self(
            id: $supplierDebitEloquentModel->id,
            invoice_no: $supplierDebitEloquentModel->invoice_no,
            description: $supplierDebitEloquentModel->description,
            is_gst_inclusive: $supplierDebitEloquentModel->is_gst_inclusive,
            amount: $supplierDebitEloquentModel->amount,
            total_amount: $supplierDebitEloquentModel->total_amount,
            gst_amount: $supplierDebitEloquentModel->gst_amount,
            invoice_date: $supplierDebitEloquentModel->invoice_date,
            pdf_path: $supplierDebitEloquentModel->pdf_path,
            vendor_id: $supplierDebitEloquentModel->vendor_id,
            sale_report_id: $supplierDebitEloquentModel->sale_report_id,
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
           'vendor_id' => $this->vendor_id,
           'sale_report_id' => $this->sale_report_id,
        ];
    }
}