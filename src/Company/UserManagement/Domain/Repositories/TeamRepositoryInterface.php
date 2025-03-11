<?php

namespace Src\Company\UserManagement\Domain\Repositories;
use Src\Company\UserManagement\Domain\Model\Entities\Role;
use Src\Company\UserManagement\Application\DTO\RoleData;
use Src\Company\UserManagement\Application\DTO\TeamData;
use Src\Company\UserManagement\Domain\Model\Entities\Team;

interface TeamRepositoryInterface
{
    public function index($filters = []);

    public function findTeamById(int $id);

    public function store(Team $team,array $teamMembers): TeamData;

    public function update(Team $team,array $teamMembers): TeamData;

    public function delete(int $teamId);

}
