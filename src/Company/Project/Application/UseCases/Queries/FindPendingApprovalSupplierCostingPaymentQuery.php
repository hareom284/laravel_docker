<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingPaymentRepositoryInterface;

class FindPendingApprovalSupplierCostingPaymentQuery implements QueryInterface
{
    private SupplierCostingPaymentRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(SupplierCostingPaymentRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findEventById', ProjectPolicy::class);
        return $this->repository->getPendingApprovalSupplierCostingPayment();
    }
}