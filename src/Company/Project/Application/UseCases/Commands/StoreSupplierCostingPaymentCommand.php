<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SupplierCostingPayment;
use Src\Company\Project\Domain\Repositories\SupplierCostingPaymentRepositoryInterface;

class StoreSupplierCostingPaymentCommand implements CommandInterface
{
    private SupplierCostingPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly SupplierCostingPayment $supplierCostingPayment,
        private readonly array $vendorInvoiceIds
    )
    {
        $this->repository = app()->make(SupplierCostingPaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->supplierCostingPayment,$this->vendorInvoiceIds);
    }
}