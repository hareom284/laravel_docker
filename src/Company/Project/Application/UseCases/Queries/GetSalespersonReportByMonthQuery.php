<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class GetSalespersonReportByMonthQuery implements QueryInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $salespersonId,
        private readonly int $month,
        private readonly int $year,
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSalespersonReportByMonth($this->salespersonId,$this->month,$this->year);
    }
}