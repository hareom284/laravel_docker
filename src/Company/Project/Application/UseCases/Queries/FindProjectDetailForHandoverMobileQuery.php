<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;

class FindProjectDetailForHandoverMobileQuery implements QueryInterface
{
    private ProjectMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(ProjectMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->projectDetailForHandover($this->projectId);
    }
}