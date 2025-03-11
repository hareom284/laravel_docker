<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class GetInvoicesFromQuickbookQuery implements QueryInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly int $qboCustomerId
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->getInvoicesFromQuickBook($this->qboCustomerId);
    }
}