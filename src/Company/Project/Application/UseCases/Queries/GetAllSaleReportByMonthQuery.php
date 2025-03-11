<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class GetAllSaleReportByMonthQuery implements QueryInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $companyId,
        private readonly int $year,
        private readonly int $month,
        private readonly string|null $startDate,
        private readonly string|null $endDate
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSaleReportByMonth($this->companyId,$this->year,$this->month,$this->startDate,$this->endDate);
    }
}