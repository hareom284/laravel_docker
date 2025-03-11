<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Repositories\BankInfoRepositoryInterface;
use Src\Company\CompanyManagement\Domain\Repositories\CompanyManagementRepositoryInterface;

class SyncBankInfoWithQuickbookCommand implements CommandInterface
{
    private BankInfoRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(BankInfoRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->syncWithQuickBooks();
    }
}