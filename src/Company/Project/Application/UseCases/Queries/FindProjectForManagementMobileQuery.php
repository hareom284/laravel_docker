<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectMobileRepositoryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindProjectForManagementMobileQuery implements QueryInterface
{
    private ProjectMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $perPage,
        private readonly int $salePerson,
        private readonly string $filterText,
        private readonly string $status,
        private readonly string $created_at,
        private readonly string $cardView
    )
    {
        $this->repository = app()->make(ProjectMobileRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getProjectsForManagement($this->perPage,$this->salePerson,$this->filterText,$this->status, $this->created_at, $this->cardView);
    }
}