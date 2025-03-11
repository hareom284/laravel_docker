<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\SupplierDebitData;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierDebitEloquentModel;

interface SupplierDebitRepositoryInterface
{
    public function index(array $filters): array;

    public function getBySaleReportId($saleReportId);

    public function getById($id);

    public function getReport(array $filters);

    public function store(SupplierDebit $supplierCredit): SupplierDebitData;

    public function update(SupplierDebit $supplierCredit): SupplierDebitEloquentModel;
}