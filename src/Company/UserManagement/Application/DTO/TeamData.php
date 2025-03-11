<?php

namespace Src\Company\UserManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\UserManagement\Infrastructure\EloquentModels\TeamEloquentModel;

class TeamData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $team_name,
        public readonly ?int $team_leader_id,
        public readonly ?int $created_by
    )
    {}

    public static function fromRequest(Request $request, ?int $team_id = null): TeamData
    {
        return new self(
            id: $team_id,
            team_name: $request->string('team_name'),
            team_leader_id: $request->integer('team_leader_id'),
            created_by: $request->integer('created_by'),
        );
    }

    public static function fromEloquent(TeamEloquentModel $teamEloquentModel): self
    {
        return new self(
            id: $teamEloquentModel->id,
            team_name: $teamEloquentModel->team_name,
            team_leader_id: $teamEloquentModel->team_leader_id,
            created_by: $teamEloquentModel->created_by,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'team_name' => $this->team_name,
            'team_leader_id' => $this->team_leader_id,
            'created_by' => $this->created_by,
        ];
    }
}
