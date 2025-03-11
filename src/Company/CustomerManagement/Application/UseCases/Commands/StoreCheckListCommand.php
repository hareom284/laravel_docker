<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Entities\CheckList;
use Src\Company\CustomerManagement\Domain\Repositories\CheckListRepositoryInterface;

class StoreCheckListCommand implements CommandInterface
{
    private CheckListRepositoryInterface $repository;

    public function __construct(
        private readonly CheckList $check_list
    )
    {
        $this->repository = app()->make(CheckListRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->store($this->check_list);
    }
}
