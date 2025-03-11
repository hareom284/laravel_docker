<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class UpdateSupplierCostingWithPaymentIdCommand implements CommandInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly array $supplierCostingIds,
        private readonly int $supplierCostingPaymentId,
    )
    {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateForPaymentId($this->supplierCostingIds,$this->supplierCostingPaymentId);
    }
}