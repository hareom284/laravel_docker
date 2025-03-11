<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerMobileRepositoryInterface;

class FindCustomerBySalepersonIdMobileQuery implements QueryInterface
{
    private CustomerMobileRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $id = null,
        private readonly array $filters
    )
    {
        $this->repository = app()->make(CustomerMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->findCustomerBySalepersonId($this->id,$this->filters);
    }
}
