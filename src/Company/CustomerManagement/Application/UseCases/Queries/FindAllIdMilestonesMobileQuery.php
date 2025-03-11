<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryMobileInterface;

class FindAllIdMilestonesMobileQuery implements QueryInterface
{
    private IdMilestoneRepositoryMobileInterface $repository;

    public function __construct(
    )
    {
        $this->repository = app()->make(IdMilestoneRepositoryMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->findAllIdMilestones();
    }
}
