<?php

namespace Src\Company\UserManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Security\Domain\Resources\PermissionResource;
use Illuminate\Support\Facades\DB;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $permissionIds = $this->permissions->pluck('id')->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => PermissionResource::collection($this->permissions),
            'permissionIds' => $permissionIds,
            'count' => DB::table('role_user')->where('role_id',[$this->id])->count(),
        ];
    }
}
