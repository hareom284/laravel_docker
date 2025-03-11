<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\SupplierCostingData;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

interface SupplierCostingRepositoryInterface
{
    public function index(array $filters);
    
    public function getByProjectId($projectId);

    public function getByVendorAndProject($vendorId,$projectId);

    public function getById($id);

    public function getReport(array $filters);

    public function store(SupplierCosting $supplierCosting): SupplierCostingData;

    public function importFromQbo(int $projectId);

    public function storeWithQbo(int $companyId);

    public function update(SupplierCosting $supplierCosting): SupplierCostingEloquentModel;
    
    public function updateForPaymentId($supplierCostingIds,$paymentId);

    public function verify(int $id,int $verifyBy);

    public function approve(int $id);

    public function checkSameCompany(array $supplierCostingIds);

    public function destroy(int $supplier_costing_id) : void;

}