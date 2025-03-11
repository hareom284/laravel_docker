<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCreditRepositoryInterface;

class FindSupplierCreditBySaleReportIdQuery implements QueryInterface
{
    private SupplierCreditRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleReportId,
    )
    {
        $this->repository = app()->make(SupplierCreditRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findEventById', ProjectPolicy::class);
        return $this->repository->getBySaleReportId($this->saleReportId);
    }
}