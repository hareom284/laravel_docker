<?php

namespace Src\Company\StaffManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonMonthlyKpiMobileRepositoryInterface;

class FindSalepersonMonthlyKpiByMonthMobileQuery implements QueryInterface
{
    private SalepersonMonthlyKpiMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleperson_id,
        private readonly ?int $year = null,
        private readonly ?int $month = null,
    )
    {
        $this->repository = app()->make(SalepersonMonthlyKpiMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index($this->saleperson_id,$this->year,$this->month);
    }
}