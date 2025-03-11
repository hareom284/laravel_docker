<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class StoreSupplierCostingWithQboCommand implements CommandInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->importFromQbo($this->projectId);
    }
}