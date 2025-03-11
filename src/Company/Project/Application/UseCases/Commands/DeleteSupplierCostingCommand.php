<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class DeleteSupplierCostingCommand implements CommandInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly int $supplier_costing_id
    )
    {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->destroy($this->supplier_costing_id);
    }
}