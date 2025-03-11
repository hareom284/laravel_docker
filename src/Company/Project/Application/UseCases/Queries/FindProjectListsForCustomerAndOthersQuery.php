<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindProjectListsForCustomerAndOthersQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        // private readonly int $perPage,
        // private readonly int $salePerson,
        private readonly array $filters
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getProjectListsForOthers($this->filters);
    }
}