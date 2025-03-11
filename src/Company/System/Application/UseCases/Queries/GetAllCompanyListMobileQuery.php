<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\CompanyMobileRepositoryInterface;

class GetAllCompanyListMobileQuery implements QueryInterface
{
    private CompanyMobileRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters

    )
    {
        $this->repository = app()->make(CompanyMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getCompanies($this->filters);
    }
}