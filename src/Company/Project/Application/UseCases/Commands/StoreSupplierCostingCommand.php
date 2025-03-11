<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SupplierCosting;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class StoreSupplierCostingCommand implements CommandInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly SupplierCosting $supplierCosting,
    )
    {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->supplierCosting);
    }
}