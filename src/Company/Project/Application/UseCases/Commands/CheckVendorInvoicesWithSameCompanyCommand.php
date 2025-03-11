<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingRepositoryInterface;

class CheckVendorInvoicesWithSameCompanyCommand implements CommandInterface
{
    private SupplierCostingRepositoryInterface $repository;

    public function __construct(
        private readonly array $invoicesId
    ) {
        $this->repository = app()->make(SupplierCostingRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', ProjectPolicy::class);
        return $this->repository->checkSameCompany($this->invoicesId);
    }
}
