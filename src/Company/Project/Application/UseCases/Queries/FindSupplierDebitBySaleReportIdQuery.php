<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface;

class FindSupplierDebitBySaleReportIdQuery implements QueryInterface
{
    private SupplierDebitRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleReportId,
    )
    {
        $this->repository = app()->make(SupplierDebitRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findEventById', ProjectPolicy::class);
        return $this->repository->getBySaleReportId($this->saleReportId);
    }
}