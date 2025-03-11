<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Model\Entities\Team;
use Src\Company\UserManagement\Domain\Repositories\TeamRepositoryInterface;

class UpdateTeamCommand implements CommandInterface
{
    private TeamRepositoryInterface $repository;

    public function __construct(
        private readonly Team $team,
        private readonly array $teamMembers
    )
    {
        $this->repository = app()->make(TeamRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeRole', UserPolicy::class);
        return $this->repository->update($this->team,$this->teamMembers);
    }
}
