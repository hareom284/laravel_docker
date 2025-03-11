<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Entities\IdMilestone;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface;

class StoreIdMilestonesCommand implements CommandInterface
{
    private IdMilestoneRepositoryInterface $repository;

    public function __construct(
        private readonly IdMilestone $idMilestone
    )
    {
        $this->repository = app()->make(IdMilestoneRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->store($this->idMilestone);
    }
}
