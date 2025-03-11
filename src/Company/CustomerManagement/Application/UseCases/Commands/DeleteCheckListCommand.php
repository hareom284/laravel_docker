<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CheckListRepositoryInterface;

class DeleteCheckListCommand implements CommandInterface
{
    private CheckListRepositoryInterface $repository;

    public function __construct(
        private readonly int $checklist_id
    )
    {
        $this->repository = app()->make(CheckListRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->delete($this->checklist_id);
    }
}
