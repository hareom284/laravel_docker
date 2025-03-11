<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CompanyManagement\Domain\Repositories\QboExpenseTypeRepositoryInterface;

class SyncExpenseTypeWithQuickbookCommand implements CommandInterface
{
    private QboExpenseTypeRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(QboExpenseTypeRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->syncWithQuickBooks();
    }
}