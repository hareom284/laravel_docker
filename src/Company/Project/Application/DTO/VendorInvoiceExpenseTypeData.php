<?php

namespace Src\Company\Project\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\VendorInvoiceExpenseTypeEloquentModel;

class VendorInvoiceExpenseTypeData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly int $project_related,
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): VendorInvoiceExpenseTypeData
    {
        return new self(
            id: $id,
            name: $request->string('name'),
            project_related: $request->integer('project_related'),
        );
    }

    public static function fromEloquent(VendorInvoiceExpenseTypeEloquentModel $vendorInvoiceExpenseTypeEloquentModel): self
    {
        return new self(
            id: $vendorInvoiceExpenseTypeEloquentModel->id,
            name: $vendorInvoiceExpenseTypeEloquentModel->name,
            project_related: $vendorInvoiceExpenseTypeEloquentModel->project_related,
        );
    }

    public function toArray(): array
    {
        return [
           'id' => $this->id,
           'name' => $this->name,
           'project_related' => $this->project_related
        ];
    }
}