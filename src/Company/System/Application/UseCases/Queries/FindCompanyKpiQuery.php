<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\CompanyKpiRepositoryInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class FindCompanyKpiQuery implements QueryInterface
{
    private CompanyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly int $company_id,
    )
    {
        $this->repository = app()->make(CompanyKpiRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getKpiRecords($this->company_id);
    }
}