<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Application\DTO\SupplierDebitData;
use Src\Company\Project\Application\DTO\VendorInvoiceExpenseTypeData;
use Src\Company\Project\Application\Mappers\SupplierCreditMapper;
use Src\Company\Project\Application\Mappers\SupplierDebitMapper;
use Src\Company\Project\Application\Mappers\VendorInvoiceExpenseTypeMapper;
use Src\Company\Project\Domain\Model\Entities\SupplierCredit;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Domain\Model\Entities\VendorInvoiceExpenseType;
use Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface;
use Src\Company\Project\Domain\Repositories\VendorInvoiceExpenseTypeRepositoryInterface;
use Src\Company\Project\Domain\Resources\SupplierDebitResource;
use Src\Company\Project\Domain\Resources\VendorInvoiceExpenseTypeResource;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\VendorInvoiceExpenseTypeEloquentModel;

class VendorInvoiceExpenseTypeRepository implements VendorInvoiceExpenseTypeRepositoryInterface
{
    public function index(array $filters): array
    {
        $perPage = $filters['perPage'] ?? 10;

        $query = VendorInvoiceExpenseTypeEloquentModel::query();

        $lists = $query->orderBy('id', 'DESC')->paginate($perPage);

        $expenseTypes =  VendorInvoiceExpenseTypeResource::collection($lists);

        $links = [
            'first' => $lists->url(1),
            'last' => $lists->url($lists->lastPage()),
            'prev' => $lists->previousPageUrl(),
            'next' => $lists->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $lists->currentPage(),
            'from' => $lists->firstItem(),
            'last_page' => $lists->lastPage(),
            'path' => $lists->url($lists->currentPage()),
            'per_page' => $perPage,
            'to' => $lists->lastItem(),
            'total' => $lists->total(),
        ];

        $responseData['data'] = $expenseTypes;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function list()
    {
        $expenseTypes = VendorInvoiceExpenseTypeEloquentModel::all();

        return VendorInvoiceExpenseTypeResource::collection($expenseTypes);
    }

    public function show(int $id): VendorInvoiceExpenseTypeData
    {
        $vendorInvoiceExpenseType = VendorInvoiceExpenseTypeEloquentModel::findOrFail($id);

        return VendorInvoiceExpenseTypeMapper::fromEloquent($vendorInvoiceExpenseType);
    }

    public function store(VendorInvoiceExpenseType $vendorInvoiceExpenseType): VendorInvoiceExpenseTypeData
    {
        $storeVendorInvoiceExpenseType = VendorInvoiceExpenseTypeMapper::toEloquent($vendorInvoiceExpenseType);

        $storeVendorInvoiceExpenseType->save();

        return VendorInvoiceExpenseTypeMapper::fromEloquent($storeVendorInvoiceExpenseType);
    }

    
    public function update(VendorInvoiceExpenseType $vendorInvoiceExpenseType): VendorInvoiceExpenseTypeEloquentModel
    {
        $vendorInvoiceExpenseTypeModel = VendorInvoiceExpenseTypeEloquentModel::findOrFail($vendorInvoiceExpenseType->id);

        $vendorInvoiceExpenseTypeModel->name = $vendorInvoiceExpenseType->name;

        $vendorInvoiceExpenseTypeModel->project_related = $vendorInvoiceExpenseType->project_related;

        $vendorInvoiceExpenseTypeModel->save();

        return $vendorInvoiceExpenseTypeModel;
    }

    public function destroy($id): bool
    {
        $vendorInvoiceExpenseType = VendorInvoiceExpenseTypeEloquentModel::findOrFail($id);

        return $vendorInvoiceExpenseType->delete();
    }
}