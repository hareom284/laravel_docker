<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class FindCustomerByManagerIdQuery implements QueryInterface
{
    private CustomerRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $id = null,
        private readonly array $filters
    )
    {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->findCustomerByManagerId($this->id,$this->filters);
    }
}
