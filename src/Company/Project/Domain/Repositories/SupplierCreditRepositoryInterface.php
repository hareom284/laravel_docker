<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\SupplierCreditData;
use Src\Company\Project\Domain\Model\Entities\SupplierCredit;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCreditEloquentModel;

interface SupplierCreditRepositoryInterface
{
    public function index(array $filters): array;

    public function getBySaleReportId($saleReportId);

    public function getById($id);

    public function getReport(array $filters);

    public function store(SupplierCredit $supplierCredit): SupplierCreditData;

    public function update(SupplierCredit $supplierCredit): SupplierCreditEloquentModel;
}