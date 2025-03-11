<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class GetNewProjectListQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $perPage,
        private readonly int $salePerson,
        private readonly int $companyId,
        private readonly string $filterText,
        private readonly string $status,
        private readonly ?array $filters
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getNewProjectList($this->perPage, $this->salePerson, $this->companyId, $this->filterText, $this->status, $this->filters);
    }
}