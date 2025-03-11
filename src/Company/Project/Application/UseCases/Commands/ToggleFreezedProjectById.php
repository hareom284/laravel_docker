<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class ToggleFreezedProjectById implements CommandInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    ) {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->ToggleFreezedProjectById($this->projectId);
    }
}
