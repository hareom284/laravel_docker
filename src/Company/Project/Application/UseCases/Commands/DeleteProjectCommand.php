<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Project;
// use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class DeleteProjectCommand implements CommandInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $project_id
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', ProjectPolicy::class);
        return $this->repository->destroy($this->project_id);
    }
}