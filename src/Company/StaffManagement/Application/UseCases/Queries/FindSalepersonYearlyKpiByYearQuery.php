<?php

namespace Src\Company\StaffManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonYearlyKpiRepositoryInterface;

class FindSalepersonYearlyKpiByYearQuery implements QueryInterface
{
    private SalepersonYearlyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleperson_id,
        private readonly int $year,
    )
    {
        $this->repository = app()->make(SalepersonYearlyKpiRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getKpiRecordsByYear($this->saleperson_id,$this->year);
    }
}