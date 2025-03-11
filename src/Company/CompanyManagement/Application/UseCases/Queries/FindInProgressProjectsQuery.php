<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindInProgressProjectsQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getInProgressProjects();
    }
}