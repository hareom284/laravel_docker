<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindProjectForManagementQuery implements QueryInterface
{
    private ProjectRepositoryInterface $repository;

    public function __construct(
        private readonly int $perPage,
        private readonly int $salePerson,
        private readonly string $filterText,
        private readonly string $status,
        private readonly string $created_at
    )
    {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
    }

    public function handle(): mixed
    {
        return $this->repository->getProjectsForManagement($this->perPage,$this->salePerson,$this->filterText,$this->status, $this->created_at);
    }
}