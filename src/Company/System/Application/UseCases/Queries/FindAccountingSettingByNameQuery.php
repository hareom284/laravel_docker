<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\AccountingSettingRepositoryInterface;

class FindAccountingSettingByNameQuery implements QueryInterface
{
    private AccountingSettingRepositoryInterface $repository;

    public function __construct(
        private readonly int $companyId
    )
    {
        $this->repository = app()->make(AccountingSettingRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findByCompany($this->companyId);
    }
}