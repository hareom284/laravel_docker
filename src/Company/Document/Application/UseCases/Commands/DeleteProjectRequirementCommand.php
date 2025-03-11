<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\ProjectRequirementRepositoryInterface;

class DeleteProjectRequirementCommand implements CommandInterface
{
    private ProjectRequirementRepositoryInterface $repository;

    public function __construct(
        private readonly int $requirement_id
    )
    {
        $this->repository = app()->make(ProjectRequirementRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteProjectRequirement', DocumentPolicy::class);
        return $this->repository->delete($this->requirement_id);
    }
}