<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\ProjectRequirementData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\ProjectRequirementRepositoryInterface;

class FindProjectRequirementByIdQuery implements QueryInterface
{
    private ProjectRequirementRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(ProjectRequirementRepositoryInterface::class);
    }

    public function handle(): ProjectRequirementData
    {
        // authorize('findProjectRequirementById', DocumentPolicy::class);
        return $this->repository->findRequirementById($this->id);
    }
}