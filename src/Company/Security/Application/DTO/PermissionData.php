<?php

namespace Src\Company\Security\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Security\Infrastructure\EloquentModels\PermissionEloquentModel;

class PermissionData
{

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {
    }

    public static function fromRequest(Request $request, $permission_id = null): PermissionData
    {

        return new self(
            id: $permission_id,
            name: $request->name,
            description: $request->description
        );
    }

    public static function fromEloquent(PermissionEloquentModel $permission): self
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
            description: $permission->description
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
