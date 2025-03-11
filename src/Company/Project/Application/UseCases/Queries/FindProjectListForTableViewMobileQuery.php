<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;

class FindProjectListForTableViewMobileQuery implements QueryInterface
{
    private ProjectMobileRepositoryInterface $repository;

    public function __construct(
        // private readonly int $perPage,
        // private readonly int $salePerson,
        private readonly array $filters
    )
    {
        $this->repository = app()->make(ProjectMobileRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getProjectListForSaleperson($this->filters);
    }
}