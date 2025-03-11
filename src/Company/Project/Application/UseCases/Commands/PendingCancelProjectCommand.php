<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class PendingCancelProjectCommand implements CommandInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    ) {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', ProjectPolicy::class);
        return $this->repository->pendingCancelProject($this->id);
    }
}
