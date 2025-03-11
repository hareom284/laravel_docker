<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CheckListRepositoryInterface;

class FindCheckListByCustomerIdQuery implements QueryInterface
{
    private CheckListRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(CheckListRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->checkListByCustomerId($this->id);
    }
}
