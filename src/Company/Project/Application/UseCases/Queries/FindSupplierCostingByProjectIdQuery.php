<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class FindSupplierCostingByProjectIdQuery implements QueryInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getByProjectId($this->projectId);
    }
}