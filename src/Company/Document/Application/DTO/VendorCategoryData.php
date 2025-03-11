<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\VendorCategoryEloquentModel;

class VendorCategoryData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $type
    )
    {}

    public static function fromRequest(Request $request, ?int $vendor_id = null): VendorCategoryData
    {
        return new self(
            id: $vendor_id,
            type: $request->string('type')
        );
    }

    public static function fromEloquent(VendorCategoryEloquentModel $vendor): self
    {
        return new self(
            id: $vendor->id,
            type: $vendor->type
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type
        ];
    }
}