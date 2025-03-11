<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class GetGroupSalepersonLeadManagementListQuery implements QueryInterface
{
    private CustomerRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $mgr_id = null,
        private readonly array $filters
    ) {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->getGroupSalepersonLeadManagementList($this->mgr_id, $this->filters);
    }
}
