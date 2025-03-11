<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface;

class FindAllIdMilestoneActionsQuery implements QueryInterface
{
    private IdMilestoneRepositoryInterface $repository;

    public function __construct(
    )
    {
        $this->repository = app()->make(IdMilestoneRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findAllIdMilestoneActions();
    }
}
