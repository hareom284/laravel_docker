<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;

class QuotationTemplateItemsData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly int $index,
        public readonly string $unit_of_measurement,
        public readonly ?int $section_id,
        public readonly ?int $area_of_work_id,
        public readonly int $price_without_gst,
        public readonly int $price_with_gst,
        public readonly int $cost_price,
        public readonly int $profit_margin,
        public readonly ?int $salesperson_id,
    ) {
    }

    public static function fromRequest(Request $request, ?int $quotation_template_items_id = null): QuotationTemplateItemsData
    {
        return new self(
            id: $quotation_template_items_id,
            description: $request->string('description'),
            index: $request->integer('index'),
            unit_of_measurement: $request->string('unit_of_measurement'),
            section_id: $request->integer('section_id'),
            area_of_work_id: $request->integer('area_of_work_id'),
            price_without_gst: $request->integer('price_without_gst'),
            price_with_gst: $request->integer('price_with_gst'),
            cost_price: $request->integer('cost_price'),
            profit_margin: $request->integer('profit_margin'),
            salesperson_id: $request->integer('salesperson_id')
        );
    }

    public static function fromEloquent(QuotationTemplateItemsEloquentModel $quotationTemplateItemsEloquent): self
    {
        return new self(
            id: $quotationTemplateItemsEloquent->id,
            description: $quotationTemplateItemsEloquent->description,
            index: $quotationTemplateItemsEloquent->index,
            unit_of_measurement: $quotationTemplateItemsEloquent->unit_of_measurement,
            section_id: $quotationTemplateItemsEloquent->section_id,
            area_of_work_id: $quotationTemplateItemsEloquent->area_of_work_id,
            price_without_gst: $quotationTemplateItemsEloquent->price_without_gst,
            price_with_gst: $quotationTemplateItemsEloquent->price_with_gst,
            cost_price: $quotationTemplateItemsEloquent->cost_price,
            profit_margin: $quotationTemplateItemsEloquent->profit_margin,
            salesperson_id: $quotationTemplateItemsEloquent->salesperson_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'index' => $this->index,
            'unit_of_measurement' => $this->unit_of_measurement,
            'section_id' => $this->section_id,
            'area_of_work_id' => $this->area_of_work_id,
            'price_without_gst' => $this->price_without_gst,
            'price_with_gst' => $this->price_with_gst,
            'cost_price' => $this->cost_price,
            'profit_margin' => $this->profit_margin,
            'salesperson_id' => $this->salesperson_id
        ];
    }
}
