<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\ProjectPortfolioRepositoryInterface;

class GetProjectByProjectPortfolioId implements QueryInterface
{
    private ProjectPortfolioRepositoryInterface $repository;

    public function __construct(
        private readonly int $sale_person_id,
    ) {
        $this->repository = app()->make(ProjectPortfolioRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getProjectPortfolioBySalePerson($this->sale_person_id);
    }
}
