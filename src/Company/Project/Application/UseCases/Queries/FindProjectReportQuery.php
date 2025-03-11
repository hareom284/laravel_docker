<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectReportRepositoryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class FindProjectReportQuery implements QueryInterface
{
    private ProjectReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(ProjectReportRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getProjectReport($this->projectId);
    }
}