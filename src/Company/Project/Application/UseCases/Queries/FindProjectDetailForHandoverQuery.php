<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class FindProjectDetailForHandoverQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->projectDetailForHandover($this->projectId);
    }
}