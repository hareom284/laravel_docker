<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class GetSupplierCostingWithVendorAndProjectQuery implements QueryInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly int $vendorId,
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->getByVendorAndProject($this->vendorId, $this->projectId);
    }
}