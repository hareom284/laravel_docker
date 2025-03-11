<?php

namespace Src\Company\StaffManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonMonthlyKpiRepositoryInterface;

class FindSalepersonMonthlyKpiByMonthQuery implements QueryInterface
{
    private SalepersonMonthlyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleperson_id,
        private readonly int $year,
        private readonly int $month,
    )
    {
        $this->repository = app()->make(SalepersonMonthlyKpiRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getKpiRecordsByMonth($this->saleperson_id,$this->year,$this->month);
    }
}