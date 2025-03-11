<?php

namespace Src\Company\Security\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Security\Infrastructure\EloquentModels\RoleEloquentModel;

class RoleData
{

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {
    }

    public static function fromRequest(Request $request, $role_id = null): RoleData
    {

        return new self(
            id: $role_id,
            name: $request->name,
            description: $request->description
        );
    }

    public static function fromEloquent(RoleEloquentModel $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            description: $role->description
        );
    }

    public function toArray(): array
    {
        return [
            "id" =>  $this->id,
            "name" =>  $this->name,
            "description" =>  $this->description
        ];
    }
}
