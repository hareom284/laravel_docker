<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class GetDefaultCompanyQuery implements QueryInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getDefaultCompany();
    }
}