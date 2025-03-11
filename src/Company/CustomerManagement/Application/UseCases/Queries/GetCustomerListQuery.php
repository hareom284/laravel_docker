<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class GetCustomerListQuery implements QueryInterface
{

    private CustomerRepositoryInterface $repository;
    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getCustomers($this->filters);
    }
}
