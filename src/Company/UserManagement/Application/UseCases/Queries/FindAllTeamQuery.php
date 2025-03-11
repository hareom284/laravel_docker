<?php

namespace Src\Company\UserManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\UserManagement\Domain\Repositories\TeamRepositoryInterface;

class FindAllTeamQuery implements QueryInterface
{
    private TeamRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters,
    )
    {
        $this->repository = app()->make(TeamRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index($this->filters);
    }
}
