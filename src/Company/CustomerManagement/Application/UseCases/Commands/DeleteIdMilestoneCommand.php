<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface;

class DeleteIdMilestoneCommand implements CommandInterface
{
    private IdMilestoneRepositoryInterface $repository;

    public function __construct(
        private readonly int $id_milestone_id
    )
    {
        $this->repository = app()->make(IdMilestoneRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteCompany', UserPolicy::class);
        return $this->repository->delete($this->id_milestone_id);
    }
}
