<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CompanyManagement\Domain\Repositories\BankInfoRepositoryInterface;

class GetAllBankInfosQuery implements QueryInterface
{
    private BankInfoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(BankInfoRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllBankInfos();
    }
}