<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class GetManagerLeadManagementListQuery implements QueryInterface
{
    private CustomerRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $id = null,
        private readonly array $filters
    ) {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->getManagerLeadManagementList($this->id, $this->filters);
    }
}
