<?php

namespace Src\Company\UserManagement\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\UserManagement\Domain\Model\Entities\Team;
use Src\Company\UserManagement\Infrastructure\EloquentModels\TeamEloquentModel;

class TeamMapper
{
    public static function fromRequest(Request $request, ?int $team_id = null): Team
    {
        return new Team(
            id: $team_id,
            team_name: $request->string('team_name'),
            team_leader_id: $request->integer('team_leader_id'),
            created_by: $request->integer('created_by'),
        );
    }

    public static function fromEloquent(TeamEloquentModel $teamEloquent): Team
    {
        return new Team(
            id: $teamEloquent->id,
            team_name: $teamEloquent->team_name,
            team_leader_id: $teamEloquent->team_leader_id,
            created_by: $teamEloquent->created_by,
        );
    }

    public static function toEloquent(Team $team): TeamEloquentModel
    {
        $teamEloquent = new TeamEloquentModel();
        if ($team->id) {
            $teamEloquent = TeamEloquentModel::query()->findOrFail($team->id);
        }
        $teamEloquent->team_name = $team->team_name;
        $teamEloquent->team_leader_id = $team->team_leader_id;
        $teamEloquent->created_by = $team->created_by;
        return $teamEloquent;
    }
}
