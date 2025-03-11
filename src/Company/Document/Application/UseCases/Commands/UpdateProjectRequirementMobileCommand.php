<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Illuminate\Http\Request;
use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface;

class UpdateProjectRequirementMobileCommand implements CommandInterface
{
    private ProjectRequirementMobileRepositoryInterface $repository;

    public function __construct(
        private readonly ProjectRequirement $projectRequirement,
        private readonly Request $request
    )
    {
        $this->repository = app()->make(ProjectRequirementMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->projectRequirement, $this->request);
    }
}