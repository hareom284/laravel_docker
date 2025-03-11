<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class GetAllCustomerPaymentQuery implements QueryInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->index($this->filters);
    }
}