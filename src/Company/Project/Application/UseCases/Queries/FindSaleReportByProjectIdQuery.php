<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class FindSaleReportByProjectIdQuery implements QueryInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSaleReportByProjectId($this->projectId);
    }
}