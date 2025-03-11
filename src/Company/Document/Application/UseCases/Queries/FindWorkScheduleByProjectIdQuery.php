<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\WorkScheduleRepositoryInterface;

class FindWorkScheduleByProjectIdQuery implements QueryInterface
{
    private WorkScheduleRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(WorkScheduleRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDocumentById', DocumentPolicy::class);
        return $this->repository->getWorkSchedules($this->projectId);
    }
}