<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerMobileRepositoryInterface;

class FindCustomerByIdMobileQuery implements QueryInterface
{
    private CustomerMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(CustomerMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findCustomerById($this->id);
    }
}
