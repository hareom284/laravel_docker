<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class GetAllSaleReportByYearQuery implements QueryInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $companyId,
        private readonly int $year,
        private readonly string|null $startDate,
        private readonly string|null $endDate
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSaleReportByYear($this->companyId,$this->year,$this->startDate,$this->endDate);
    }
}