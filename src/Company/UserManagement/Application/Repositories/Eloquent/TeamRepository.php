<?php

namespace Src\Company\UserManagement\Application\Repositories\Eloquent;

use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Application\DTO\TeamData;
use Src\Company\UserManagement\Application\Mappers\TeamMapper;
use Src\Company\UserManagement\Domain\Model\Entities\Team;
use Src\Company\UserManagement\Domain\Repositories\TeamRepositoryInterface;
use Src\Company\UserManagement\Domain\Resources\TeamResource;
use Src\Company\UserManagement\Infrastructure\EloquentModels\TeamEloquentModel;

class TeamRepository implements TeamRepositoryInterface
{
    public function index($filters = [])
    {
        //roles list
        $perPage = $filters['perPage'] ?? 10;

        $teamEloquent = TeamEloquentModel::query()->filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $team = TeamResource::collection($teamEloquent);

        $links = [
            'first' => $team->url(1),
            'last' => $team->url($team->lastPage()),
            'prev' => $team->previousPageUrl(),
            'next' => $team->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $team->currentPage(),
            'from' => $team->firstItem(),
            'last_page' => $team->lastPage(),
            'path' => $team->url($team->currentPage()),
            'per_page' => $perPage,
            'to' => $team->lastItem(),
            'total' => $team->total(),
        ];
        $responseData['data'] = $team;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function findTeamById(int $id)
    {
        $teamEloquent = TeamEloquentModel::query()->findOrFail($id);

        $team = new TeamResource($teamEloquent);

        return $team;
    }

    public function store(Team $team,array $teamMemebers): TeamData
    {

        $teamEloquent = TeamMapper::toEloquent($team);

        $teamEloquent->save();

        // Attach permission IDs to the role using the role_permissions pivot table
        $teamEloquent->teamMemebers()->attach($teamMemebers);

        foreach ($teamMemebers as $teamMemeber) {
            $staff = StaffEloquentModel::query()->where('user_id',$teamMemeber)->first();

            $staff->mgr_id = $teamEloquent->team_leader_id;
            $staff->update();

        }

        return TeamData::fromEloquent($teamEloquent);
    }

    public function update(Team $team,array $teamMemebers): TeamData
    {
        $teamEloquent = TeamMapper::toEloquent($team);

        $teamEloquent->save();

        $teamEloquent->teamMemebers()->sync($teamMemebers);

        foreach ($teamMemebers as $teamMemeber) {
            $staff = StaffEloquentModel::query()->where('user_id',$teamMemeber)->first();
            
            $staff->mgr_id = $teamEloquent->team_leader_id;
            $staff->update();

        }

        return TeamData::fromEloquent($teamEloquent);
    }

    public function delete(int $teamId): void
    {
        $teamEloquent = TeamEloquentModel::query()->findOrFail($teamId);
        $teamEloquent->delete();
    }
}
