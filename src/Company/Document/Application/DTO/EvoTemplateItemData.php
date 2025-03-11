<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateItemEloquentModel;

class EvoTemplateItemData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly float $unit_rate_without_gst,
        public readonly float $unit_rate_with_gst
    )
    {}

    public static function fromRequest(Request $request, ?int $item_id = null): EvoTemplateItemData
    {
        return new self(
            id: $item_id,
            description: $request->string('description'),
            unit_rate_without_gst: $request->float('unit_rate_without_gst'),
            unit_rate_with_gst: $request->float('unit_rate_with_gst')
        );
    }

    public static function fromEloquent(EvoTemplateItemEloquentModel $itemEloquent): self
    {
        return new self(
            id: $itemEloquent->id,
            description: $itemEloquent->description,
            unit_rate_without_gst: $itemEloquent->unit_rate_without_gst,
            unit_rate_with_gst: $itemEloquent->unit_rate_with_gst
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'unit_rate_without_gst' => $this->unit_rate_without_gst,
            'unit_rate_with_gst' => $this->unit_rate_with_gst
        ];
    }
}