<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Application\DTO\ProjectData;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindAllProjectsQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle(): ProjectData
    {
        return $this->repository->findProjectAll($this->id);
    }
}