<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\CompanyKpiRepositoryInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class FindCompanyKpiByYearQuery implements QueryInterface
{
    private CompanyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly int $company_id,
        private readonly int $year,
    )
    {
        $this->repository = app()->make(CompanyKpiRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getKpiRecordsByYear($this->company_id,$this->year);
    }
}