<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Application\DTO\SupplierCostingPaymentData;
use Src\Company\Project\Domain\Model\Entities\SupplierCostingPayment;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingPaymentEloquentModel;

class SupplierCostingPaymentMapper
{
    public static function fromRequest(Request $request, ?int $id = null): SupplierCostingPayment
    {
        if ($request->hasFile('managerSignature')) {

            $fileName =  time() . '.' . $request->managerSignature->getClientOriginalExtension();

            $filePath = 'vendor_payments_manager_sign/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($request->managerSignature));

            $documentFile = $fileName;

        } else {

            $documentFile = null;
        }

        $user = auth('sanctum')->user();

        return new SupplierCostingPayment(
            id : $id,
            bank_transaction_id : $request->bank_transaction_id,
            payment_date : $request->payment_date,
            payment_type : $request->payment_type,
            amount : (float) $request->amount,
            remark : $request->filled('remark') ? $request->input('remark') : null,
            manager_signature : $documentFile,
            payment_method : $request->payment_method,
            status : $request->status,
            payment_made_by : $user->id,
            signed_by_manager_id: $request->filled('signed_by_manager_id') ? $request->input('signed_by_manager_id') : null
        );
    }

    public static function fromEloquent(SupplierCostingPaymentEloquentModel $supplierCostingPaymentEloquentModel): SupplierCostingPaymentData
    {
        return new SupplierCostingPaymentData(
            id: $supplierCostingPaymentEloquentModel->id,
            bank_transaction_id: $supplierCostingPaymentEloquentModel->bank_transaction_id,
            payment_date: $supplierCostingPaymentEloquentModel->payment_date,
            payment_type: $supplierCostingPaymentEloquentModel->payment_type,
            amount: $supplierCostingPaymentEloquentModel->amount,
            remark: $supplierCostingPaymentEloquentModel->remark,
            manager_signature: $supplierCostingPaymentEloquentModel->manager_signature,
            payment_method: $supplierCostingPaymentEloquentModel->payment_method,
            status: $supplierCostingPaymentEloquentModel->status,
            payment_made_by: $supplierCostingPaymentEloquentModel->payment_made_by,
            signed_by_manager_id: $supplierCostingPaymentEloquentModel->signed_by_manager_id,
        );
    }

    public static function toEloquent(SupplierCostingPayment $supplierCostingPayment): SupplierCostingPaymentEloquentModel
    {
        $supplierCostingPaymentEloquent = new SupplierCostingPaymentEloquentModel();

        if ($supplierCostingPayment->id) {
            $supplierCostingPaymentEloquent = SupplierCostingPaymentEloquentModel::query()->findOrFail($supplierCostingPayment->id);
        }

        $supplierCostingPaymentEloquent->bank_transaction_id = $supplierCostingPayment->bank_transaction_id;
        $supplierCostingPaymentEloquent->payment_date = $supplierCostingPayment->payment_date;
        $supplierCostingPaymentEloquent->payment_type = $supplierCostingPayment->payment_type;
        $supplierCostingPaymentEloquent->amount = $supplierCostingPayment->amount;
        $supplierCostingPaymentEloquent->remark = $supplierCostingPayment->remark;
        $supplierCostingPaymentEloquent->manager_signature = $supplierCostingPayment->manager_signature;
        $supplierCostingPaymentEloquent->payment_method = $supplierCostingPayment->payment_method;
        $supplierCostingPaymentEloquent->status = $supplierCostingPayment->status;
        $supplierCostingPaymentEloquent->payment_made_by = $supplierCostingPayment->payment_made_by;
        $supplierCostingPaymentEloquent->signed_by_manager_id = $supplierCostingPayment->signed_by_manager_id;
        
        return $supplierCostingPaymentEloquent;
    }
}
