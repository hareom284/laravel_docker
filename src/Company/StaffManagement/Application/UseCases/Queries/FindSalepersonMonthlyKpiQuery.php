<?php

namespace Src\Company\StaffManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonMonthlyKpiRepositoryInterface;

class FindSalepersonMonthlyKpiQuery implements QueryInterface
{
    private SalepersonMonthlyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleperson_id,
    )
    {
        $this->repository = app()->make(SalepersonMonthlyKpiRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getKpiRecords($this->saleperson_id);
    }
}