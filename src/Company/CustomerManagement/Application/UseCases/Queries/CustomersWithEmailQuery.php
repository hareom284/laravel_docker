<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;

class CustomersWithEmailQuery implements QueryInterface
{
    private CustomerRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getCustomersWithEmail();
    }
}
