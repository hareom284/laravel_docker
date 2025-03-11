<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;

class GetProjectsMobileQuery implements QueryInterface
{
    private ProjectMobileRepositoryInterface $repository;

    public function __construct(
        private readonly ?string $userId,
        private readonly ?string $role,
    )
    {
        $this->repository = app()->make(ProjectMobileRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getProjects($this->userId, $this->role);
    }
}