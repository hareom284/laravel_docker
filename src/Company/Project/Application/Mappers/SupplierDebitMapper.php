<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Application\DTO\SupplierDebitData;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;

class SupplierDebitMapper
{
    public static function fromRequest(Request $request, ?int $id = null): SupplierDebit
    {
        if ($request->hasFile('document_file')) {

            $fileName =  time() . '.' . $request->document_file->getClientOriginalExtension();

            $filePath = 'supplier_debits/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

            $documentFile = $fileName;

        } else {

            $documentFile = null;
        }

        return new SupplierDebit(
            id : $id,
            invoice_no : $request->invoice_no,
            description : $request->description,
            is_gst_inclusive : $request->is_gst_inclusive,
            total_amount : (float) $request->total_amount,
            amount : (float) $request->amount,
            gst_amount : (float) $request->gst_amount,
            invoice_date : $request->invoice_date,
            pdf_path : $documentFile,
            vendor_id : $request->vendor_id,
            sale_report_id : $request->sale_report_id,
        );
    }

    public static function fromEloquent(SupplierDebitEloquentModel $supplierDebitEloquentModel): SupplierDebitData
    {
        return new SupplierDebitData(
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
            sale_report_id: $supplierDebitEloquentModel->sale_report_id
        );
    }

    public static function toEloquent(SupplierDebit $supplierDebit): SupplierDebitEloquentModel
    {
        $supplierDebitEloquentModel = new SupplierDebitEloquentModel();

        if ($supplierDebit->id) {
            $supplierDebitEloquentModel = SupplierDebitEloquentModel::query()->findOrFail($supplierDebit->id);
        }

        $supplierDebitEloquentModel->invoice_no = $supplierDebit->invoice_no;
        $supplierDebitEloquentModel->description = $supplierDebit->description;
        $supplierDebitEloquentModel->is_gst_inclusive = $supplierDebit->is_gst_inclusive;
        $supplierDebitEloquentModel->amount = $supplierDebit->amount;
        $supplierDebitEloquentModel->total_amount = $supplierDebit->total_amount;
        $supplierDebitEloquentModel->gst_amount = $supplierDebit->gst_amount;
        $supplierDebitEloquentModel->invoice_date = $supplierDebit->invoice_date;
        $supplierDebitEloquentModel->pdf_path = $supplierDebit->pdf_path;
        $supplierDebitEloquentModel->vendor_id = $supplierDebit->vendor_id;
        $supplierDebitEloquentModel->sale_report_id = $supplierDebit->sale_report_id;
        
        return $supplierDebitEloquentModel;
    }
}
