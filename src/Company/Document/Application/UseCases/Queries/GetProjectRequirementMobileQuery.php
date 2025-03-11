<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface;

class GetProjectRequirementMobileQuery implements QueryInterface
{
    private ProjectRequirementMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $project_id,
    ) {
        $this->repository = app()->make(ProjectRequirementMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getProjectRequirements($this->project_id);
    }
}
