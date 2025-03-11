<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerMobileRepositoryInterface;

class FindCustomerListWithPropertiesMobileQuery implements QueryInterface
{
    private CustomerMobileRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(CustomerMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->getCustomerListWithProperties();
    }
}
