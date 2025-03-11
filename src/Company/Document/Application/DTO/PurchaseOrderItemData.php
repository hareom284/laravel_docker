<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderItemEloquentModel;

class PurchaseOrderItemData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly string $code,
        public readonly string $quantity,
        public readonly string $size
    )
    {}

    public static function fromRequest(Request $request, ?int $item_id = null): PurchaseOrderItemData
    {
        return new self(
            id: $item_id,
            description: $request->string('description'),
            code: $request->string('code'),
            quantity: $request->string('quantity'),
            size: $request->string('size')
        );
    }

    public static function fromEloquent(PurchaseOrderItemEloquentModel $itemEloquent): self
    {
        return new self(
            id: $itemEloquent->id,
            description: $itemEloquent->description,
            code: $itemEloquent->code,
            quantity: $itemEloquent->quantity,
            size: $itemEloquent->size
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'code' => $this->code,
            'quantity' => $this->quantity,
            'size' => $this->size        ];
    }
}