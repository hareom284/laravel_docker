<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\SupplierCostingPaymentData;
use Src\Company\Project\Domain\Model\Entities\SupplierCostingPayment;

interface SupplierCostingPaymentRepositoryInterface
{
    public function index(array $filters);
    
    public function getPendingApprovalSupplierCostingPayment();

    public function SupplierCostingPaymentDetail(int $id);

    public function store(SupplierCostingPayment $supplierCostingPayment,array $vendorInvoiceIds): SupplierCostingPaymentData;

    public function managerSign($request);

}