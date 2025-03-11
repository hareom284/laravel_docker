<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\VendorInvoiceExpenseTypeData;
use Src\Company\Project\Domain\Model\Entities\VendorInvoiceExpenseType;
use Src\Company\Project\Infrastructure\EloquentModels\VendorInvoiceExpenseTypeEloquentModel;

interface VendorInvoiceExpenseTypeRepositoryInterface
{
    public function index(array $filters): array;

    public function list();

    public function show(int $id): VendorInvoiceExpenseTypeData;

    public function store(VendorInvoiceExpenseType $vendorInvoiceExpenseType): VendorInvoiceExpenseTypeData;

    public function update(VendorInvoiceExpenseType $vendorInvoiceExpenseType): VendorInvoiceExpenseTypeEloquentModel;

    public function destroy(int $id): bool;
}