<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Application\DTO\SupplierDebitData;
use Src\Company\Project\Application\DTO\VendorInvoiceExpenseTypeData;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Domain\Model\Entities\VendorInvoiceExpenseType;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\VendorInvoiceExpenseTypeEloquentModel;

class VendorInvoiceExpenseTypeMapper
{
    public static function fromRequest(Request $request, ?int $id = null): VendorInvoiceExpenseType
    {
        return new VendorInvoiceExpenseType(
            id : $id,
            name : $request->name,
            project_related : $request->project_related,
        );
    }

    public static function fromEloquent(VendorInvoiceExpenseTypeEloquentModel $vendorInvoiceExpenseTypeEloquentModel): VendorInvoiceExpenseTypeData
    {
        return new VendorInvoiceExpenseTypeData(
            id: $vendorInvoiceExpenseTypeEloquentModel->id,
            name: $vendorInvoiceExpenseTypeEloquentModel->name,
            project_related: $vendorInvoiceExpenseTypeEloquentModel->project_related,
        );
    }

    public static function toEloquent(VendorInvoiceExpenseType $vendorInvoiceExpenseType): VendorInvoiceExpenseTypeEloquentModel
    {
        $vendorInvoiceExpenseTypeEloquentModel = new VendorInvoiceExpenseTypeEloquentModel();

        if ($vendorInvoiceExpenseType->id) {
            $vendorInvoiceExpenseTypeEloquentModel = VendorInvoiceExpenseTypeEloquentModel::query()->findOrFail($vendorInvoiceExpenseType->id);
        }

        $vendorInvoiceExpenseTypeEloquentModel->name = $vendorInvoiceExpenseType->name;
        $vendorInvoiceExpenseTypeEloquentModel->project_related = $vendorInvoiceExpenseType->project_related;
        
        return $vendorInvoiceExpenseTypeEloquentModel;
    }
}
