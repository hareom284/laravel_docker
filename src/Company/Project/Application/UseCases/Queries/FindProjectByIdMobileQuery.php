<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;

class FindProjectByIdMobileQuery implements QueryInterface
{
    private ProjectMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(ProjectMobileRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->show($this->id);
    }
}