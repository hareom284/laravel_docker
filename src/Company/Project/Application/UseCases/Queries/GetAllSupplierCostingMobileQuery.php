<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingMobileRepositoryInterface;

class GetAllSupplierCostingMobileQuery implements QueryInterface
{
    private SupplierCostingMobileRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(SupplierCostingMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->index($this->filters);
    }
}
