<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Application\DTO\SupplierCostingData;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class SupplierCostingMapper
{
    public static function fromRequest(Request $request, ?int $id = null): SupplierCosting
    {
        if ($request->hasFile('document_file')) {

            $fileName =  time() . '.' . $request->document_file->getClientOriginalExtension();

            $filePath = 'supplier_costings/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

            $documentFile = $fileName;

        } else {

            // step to restore the database file name
            if(isset($request->original_file)){
                $documentFile = $request->original_file;
            } else {
                $documentFile = null;
            }
            
        }

        $authUser = auth('sanctum')->user();

        $authUserRoles = $authUser->roles()->where('name', 'Salesperson')->exists();

        $status = $authUserRoles ? 1 : 0;

        if(is_null($request->vendor_invoice_expense_type_id)){

            if (preg_match('/[a-zA-Z]/', $request->quick_book_expense_id)) {
                
                $rawQboExpenseTypeId = explode("-", $request->quick_book_expense_id);

                $expenseTypeId = $rawQboExpenseTypeId[1];
                $qboExpenseTypeId = null;

            } else {
                $expenseTypeId = null;
                $qboExpenseTypeId = $request->quick_book_expense_id;
            }

        }else{

            $qboExpenseTypeId = null;
            $expenseTypeId = $request->vendor_invoice_expense_type_id;
        }

        return new SupplierCosting(
            id : $id,
            invoice_no : $request->invoice_no,
            description : $request->description,
            payment_amt : $request->payment_amt,
            amended_amt : $request->amended_amt,
            remark : $request->remark,
            amount_paid : $request->filled('amount_paid') ? floatval($request->input('amount_paid')) : null,
            to_pay : floatval($request->input('to_pay')),
            discount_percentage : $request->discount_percentage,
            discount_amt : $request->discount_amt,
            credit_amt : $request->filled('credit_amt') ? $request->input('credit_amt') : null,
            invoice_date : $request->invoice_date,
            is_gst_inclusive : $request->is_gst_inclusive ? 1 : 0,
            gst_value : $request->gst_value,
            document_file : $documentFile,
            status : $status,
            project_id : $request->project_id ?? null,
            vendor_id : $request->vendor_id,
            quick_book_expense_id : $qboExpenseTypeId,
            vendor_invoice_expense_type_id : $expenseTypeId,
            purchase_order_id : $request->purchase_order_id ?? null,
        );
    }

    public static function fromEloquent(SupplierCostingEloquentModel $supplierCostingEloquentModel): SupplierCostingData
    {
        return new SupplierCostingData(
            id: $supplierCostingEloquentModel->id,
            invoice_no: $supplierCostingEloquentModel->invoice_no,
            description: $supplierCostingEloquentModel->description,
            payment_amt: $supplierCostingEloquentModel->payment_amt,
            amount_paid: $supplierCostingEloquentModel->amount_paid,
            amended_amt: $supplierCostingEloquentModel->amended_amt,
            remark: $supplierCostingEloquentModel->remark,
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
            vendor_invoice_expense_type_id: $supplierCostingEloquentModel->vendor_invoice_expense_type_id,
        );
    }

    public static function toEloquent(SupplierCosting $supplierCosting): SupplierCostingEloquentModel
    {
        $supplierCostingEloquent = new SupplierCostingEloquentModel();
        if ($supplierCosting->id) {
            $supplierCostingEloquent = SupplierCostingEloquentModel::query()->findOrFail($supplierCosting->id);
        }
        $supplierCostingEloquent->invoice_no = $supplierCosting->invoice_no;
        $supplierCostingEloquent->description = $supplierCosting->description;
        $supplierCostingEloquent->payment_amt = $supplierCosting->payment_amt;
        $supplierCostingEloquent->amount_paid = $supplierCosting->amount_paid;
        $supplierCostingEloquent->amended_amt = $supplierCosting->amended_amt;
        $supplierCostingEloquent->remark = $supplierCosting->remark;
        $supplierCostingEloquent->to_pay = $supplierCosting->to_pay;
        $supplierCostingEloquent->discount_percentage = $supplierCosting->discount_percentage;
        $supplierCostingEloquent->discount_amt = $supplierCosting->discount_amt;
        $supplierCostingEloquent->credit_amt = $supplierCosting->credit_amt;
        $supplierCostingEloquent->is_gst_inclusive = $supplierCosting->is_gst_inclusive;
        $supplierCostingEloquent->invoice_date = $supplierCosting->invoice_date;
        $supplierCostingEloquent->gst_value = $supplierCosting->gst_value;
        $supplierCostingEloquent->document_file = $supplierCosting->document_file;
        $supplierCostingEloquent->status = $supplierCosting->status;
        $supplierCostingEloquent->project_id = $supplierCosting->project_id;
        $supplierCostingEloquent->vendor_id = $supplierCosting->vendor_id;
        $supplierCostingEloquent->purchase_order_id = $supplierCosting->purchase_order_id;
        $supplierCostingEloquent->quick_book_expense_id = $supplierCosting->quick_book_expense_id;
        $supplierCostingEloquent->vendor_invoice_expense_type_id = $supplierCosting->vendor_invoice_expense_type_id;
        return $supplierCostingEloquent;
    }
}
