<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class QuotationTemplateItems extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly int $index,
        public readonly string $unit_of_measurement,
        public readonly string $section_name,
        public readonly int $quantity,
        public readonly string $calculation_type,
        public readonly string $area_of_work_name,
        public readonly int $price_without_gst,
        public readonly int $price_with_gst,
        public readonly int $cost_price,
        public readonly int $profit_margin,
        public readonly int $salesperson_id,
        public readonly int $parent_id
    )
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'index' => $this->index,
            'unit_of_measurement' => $this->unit_of_measurement,
            'section_name' => $this->section_name,
            'quantity' => $this->quantity,
            'calculation_type' => $this->calculation_type,
            'area_of_work_name' => $this->area_of_work_name,
            'price_without_gst' => $this->price_without_gst,
            'price_with_gst' => $this->price_with_gst,
            'cost_price' => $this->cost_price,
            'profit_margin' => $this->profit_margin,
            'salesperson_id' => $this->salesperson_id,
            'parent_id' => $this->parent_id
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}