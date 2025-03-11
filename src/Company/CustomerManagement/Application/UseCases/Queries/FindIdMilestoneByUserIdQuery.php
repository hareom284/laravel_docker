<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\IdMilestoneRepositoryInterface;

class FindIdMilestoneByUserIdQuery implements QueryInterface
{
    private IdMilestoneRepositoryInterface $repository;


    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(IdMilestoneRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findIdMilestoneByUserId($this->id);
    }
}
