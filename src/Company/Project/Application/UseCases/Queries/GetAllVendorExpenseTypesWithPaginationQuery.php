<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\VendorInvoiceExpenseTypeRepositoryInterface;

class GetAllVendorExpenseTypesWithPaginationQuery implements QueryInterface
{
    private VendorInvoiceExpenseTypeRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(VendorInvoiceExpenseTypeRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->index($this->filters);
    }
}