<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Domain\Model\Entities\SupplierCredit;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;

class SupplierCreditMapper
{
    public static function fromRequest(Request $request, ?int $id = null): SupplierCredit
    {
        if ($request->hasFile('document_file')) {

            $fileName =  time() . '.' . $request->document_file->getClientOriginalExtension();

            $filePath = 'supplier_credits/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

            $documentFile = $fileName;

        } else {

            $documentFile = null;
        }

        return new SupplierCredit(
            id : $id,
            invoice_no : $request->invoice_no,
            description : $request->description,
            is_gst_inclusive : $request->is_gst_inclusive,
            total_amount : (float) $request->total_amount,
            amount : (float) $request->amount,
            gst_amount : (float) $request->gst_amount,
            invoice_date : $request->invoice_date,
            pdf_path : $documentFile,
            quick_book_vendor_credit_id : null,
            vendor_id : $request->vendor_id,
            sale_report_id : $request->sale_report_id,
        );
    }

    public static function fromEloquent(SupplierCreditEloquentModel $supplierCreditEloquentModel): SupplierCreditData
    {
        return new SupplierCreditData(
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
            sale_report_id: $supplierCreditEloquentModel->sale_report_id
        );
    }

    public static function toEloquent(SupplierCredit $supplierCredit): SupplierCreditEloquentModel
    {
        $supplierCreditEloquent = new SupplierCreditEloquentModel();

        if ($supplierCredit->id) {
            $supplierCreditEloquent = SupplierCreditEloquentModel::query()->findOrFail($supplierCredit->id);
        }

        $supplierCreditEloquent->invoice_no = $supplierCredit->invoice_no;
        $supplierCreditEloquent->description = $supplierCredit->description;
        $supplierCreditEloquent->is_gst_inclusive = $supplierCredit->is_gst_inclusive;
        $supplierCreditEloquent->amount = $supplierCredit->amount;
        $supplierCreditEloquent->total_amount = $supplierCredit->total_amount;
        $supplierCreditEloquent->gst_amount = $supplierCredit->gst_amount;
        $supplierCreditEloquent->invoice_date = $supplierCredit->invoice_date;
        $supplierCreditEloquent->pdf_path = $supplierCredit->pdf_path;
        $supplierCreditEloquent->quick_book_vendor_credit_id = $supplierCredit->quick_book_vendor_credit_id;
        $supplierCreditEloquent->vendor_id = $supplierCredit->vendor_id;
        $supplierCreditEloquent->sale_report_id = $supplierCredit->sale_report_id;
        
        return $supplierCreditEloquent;
    }
}
