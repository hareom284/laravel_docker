<?php

namespace Src\Company\CustomerManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\RejectedReasonsEloquentModel;

class RejectedReasonData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?int $index,
        public readonly ?string $color_code
    )
    {}

    public static function fromRequest(Request $request, ?int $company_id = null): RejectedReasonData
    {
        return new self(
            id: $company_id,
            name: $request->string('name'),
            index: $request->integer('index'),
            color_code: $request->string('color_code')
        );
    }

    public static function fromEloquent(RejectedReasonsEloquentModel $rejectedReasonEloquent): self
    {
        return new self(
            id: $rejectedReasonEloquent->id,
            name: $rejectedReasonEloquent->name,
            index: $rejectedReasonEloquent->index,
            color_code: $rejectedReasonEloquent->color_code
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'index' => $this->index,
            'color_code' => $this->color_code
        ];
    }
}
