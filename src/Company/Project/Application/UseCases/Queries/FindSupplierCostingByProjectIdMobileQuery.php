<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingMobileRepositoryInterface;

class FindSupplierCostingByProjectIdMobileQuery implements QueryInterface
{
    private SupplierCostingMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(SupplierCostingMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getByProjectId($this->projectId);
    }
}