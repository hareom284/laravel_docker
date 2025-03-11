<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Src\Company\Document\Infrastructure\EloquentModels\EvoItemEloquentModel;

class EvoItemData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $item_description,
        public readonly int $quantity,
        public readonly ?float $unit_rate,
        public readonly ?float $total
    )
    {}

    public static function fromRequest(Request $request, ?int $evo_item_id = null): EvoItemData
    {
        return new self(
            id: $evo_item_id,
            item_description: $request->string('item_description'),
            quantity: $request->integer('quantity'),
            unit_rate: $request->float('unit_rate'),
            total: $request->float('total')
        );
    }

    public static function fromEloquent(EvoItemEloquentModel $evoEloquent): self
    {
        return new self(
            id: $evoEloquent->id,
            item_description: $evoEloquent->item_description,
            quantity: $evoEloquent->quantity,
            unit_rate: $evoEloquent->unit_rate,
            total: $evoEloquent->total
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'item_description' => $this->item_description,
            'quantity' => $this->quantity,
            'unit_rate' => $this->unit_rate,
            'total' => $this->total        ];
    }
}