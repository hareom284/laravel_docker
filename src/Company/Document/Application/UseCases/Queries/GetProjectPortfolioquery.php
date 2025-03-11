<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\ProjectPortfolioRepositoryInterface;

class GetProjectPortfolioquery implements QueryInterface
{
    private ProjectPortfolioRepositoryInterface $repository;

    public function __construct(
        private readonly int $project_id,
    ) {
        $this->repository = app()->make(ProjectPortfolioRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getProjectPortfolioByProjectId($this->project_id);
    }
}
