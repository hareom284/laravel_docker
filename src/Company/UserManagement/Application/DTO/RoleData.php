<?php

namespace Src\Company\UserManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;

class RoleData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $description,
    )
    {}

    public static function fromRequest(Request $request, ?int $role_id = null): RoleData
    {
        return new self(
            id: $role_id,
            name: $request->string('name'),
            description: $request->string('name'),
        );
    }

    public static function fromEloquent(RoleEloquentModel $roleEloquent): self
    {
        return new self(
            id: $roleEloquent->id,
            name: $roleEloquent->name,
            description: $roleEloquent->description,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
