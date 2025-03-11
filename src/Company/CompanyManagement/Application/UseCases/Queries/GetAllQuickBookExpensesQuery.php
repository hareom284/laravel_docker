<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CompanyManagement\Domain\Repositories\QboExpenseTypeRepositoryInterface;

class GetAllQuickBookExpensesQuery implements QueryInterface
{
    private QboExpenseTypeRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(QboExpenseTypeRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllExpenseTypes();
    }
}