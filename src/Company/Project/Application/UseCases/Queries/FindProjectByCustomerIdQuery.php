<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindProjectByCustomerIdQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $customerId,
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->projectByCustomerId($this->customerId);
    }
}