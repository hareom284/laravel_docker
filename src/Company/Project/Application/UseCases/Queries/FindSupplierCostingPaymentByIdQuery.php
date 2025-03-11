<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Application\DTO\EventData;
use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\EventRepositoryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingPaymentRepositoryInterface;

class FindSupplierCostingPaymentByIdQuery implements QueryInterface
{
    private SupplierCostingPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(SupplierCostingPaymentRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findEventById', ProjectPolicy::class);
        return $this->repository->SupplierCostingPaymentDetail($this->id);
    }
}