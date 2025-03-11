<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Repositories\TeamRepositoryInterface;

class DeleteTeamCommand implements CommandInterface
{
    private TeamRepositoryInterface $repository;

    public function __construct(
        private readonly int $teamId
    )
    {
        $this->repository = app()->make(TeamRepositoryInterface::class);
    }

    public function execute()
    {
        // authorize('deleteRole', UserPolicy::class);
        return $this->repository->delete($this->teamId);
    }
}
