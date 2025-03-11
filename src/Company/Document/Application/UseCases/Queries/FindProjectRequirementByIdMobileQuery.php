<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\ProjectRequirementData;
use Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface;

class FindProjectRequirementByIdMobileQuery implements QueryInterface
{
    private ProjectRequirementMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(ProjectRequirementMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findProjectRequirementById', DocumentPolicy::class);
        return $this->repository->findRequirementById($this->id);
    }
}