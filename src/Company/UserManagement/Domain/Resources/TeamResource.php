<?php

namespace Src\Company\UserManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Security\Domain\Resources\PermissionResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'team_name' => $this->team_name,
            'team_leader_id' => $this->team_leader_id,
            'leader' => $this->teamLead,
            'created_by' => $this->created_by,
            'members' => $this->teamMemebers
        ];
    }
}
