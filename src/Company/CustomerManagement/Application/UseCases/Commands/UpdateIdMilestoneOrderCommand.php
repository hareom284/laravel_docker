<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Entities\IdMilestone;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface;

class UpdateIdMilestoneOrderCommand implements CommandInterface
{
    private IdMilestoneRepositoryInterface $repository;

    public function __construct(
        private readonly string $idMilestones
    )
    {
        $this->repository = app()->make(IdMilestoneRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateOrder($this->idMilestones);
    }
}
