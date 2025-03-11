<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class GetPendingCancelProjectsQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(private readonly int $perPage)
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getPendingCancelProjects($this->perPage);
    }
}