<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface;

class DeleteProjectRequirementMobileCommand implements CommandInterface
{
    private ProjectRequirementMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $requirement_id
    )
    {
        $this->repository = app()->make(ProjectRequirementMobileRepositoryInterface::class);
    }

    public function execute()
    {
        // authorize('deleteProjectRequirement', DocumentPolicy::class);
        return $this->repository->delete($this->requirement_id);
    }
}