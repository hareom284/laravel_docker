<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportMobileRepositoryInterface;

class FindSalepersonKpiReportMonthMobileQuery implements QueryInterface
{
    private SaleReportMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $salespersonUserId,
        private readonly ?int $month,
        private readonly ?int $year,
    )
    {
        $this->repository = app()->make(SaleReportMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSalepersonKpiReportMonth($this->salespersonUserId,$this->month,$this->year);
    }
}