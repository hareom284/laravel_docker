<?php

namespace Src\Company\Document\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateItems;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;

class QuotationTemplateItemsMapper
{
    public static function fromRequest(Request $request, ?int $quotation_template_items_id = null): QuotationTemplateItems
    {
        return new QuotationTemplateItems(
            id: $quotation_template_items_id,
            description: $request->string('description'),
            index: $request->index ? $request->integer('index') : 0,
            unit_of_measurement: $request->string('unit_of_measurement'),
            quantity: $request->integer('quantity'),
            section_name: $request->string('section_name'),
            calculation_type: $request->string('calculation_type'),
            area_of_work_name: $request->string('area_of_work_name'),
            price_without_gst: $request->integer('price_without_gst'),
            price_with_gst: $request->integer('price_with_gst'),
            cost_price: $request->cost_price ? $request->integer('cost_price') : 0,
            profit_margin: $request->profit_margin ? $request->integer('profit_margin') : 0,
            salesperson_id: $request->salesperson_id ? $request->salesperson_id : null
        );
    }

    public static function fromEloquent(QuotationTemplateItemsEloquentModel $quotationTemplateItemsEloquent): QuotationTemplateItems
    {
        return new QuotationTemplateItems(
            id: $quotationTemplateItemsEloquent->id,
            description: $quotationTemplateItemsEloquent->description,
            index: $quotationTemplateItemsEloquent->index,
            unit_of_measurement: $quotationTemplateItemsEloquent->unit_of_measurement,
            section_name: $quotationTemplateItemsEloquent->section_name,
            quantity: $quotationTemplateItemsEloquent->quantity,
            calculation_type: $quotationTemplateItemsEloquent->calculation_type,
            area_of_work_name: $quotationTemplateItemsEloquent->area_of_work_name,
            price_without_gst: $quotationTemplateItemsEloquent->price_without_gst,
            price_with_gst: $quotationTemplateItemsEloquent->price_with_gst,
            cost_price: $quotationTemplateItemsEloquent->cost_price,
            profit_margin: $quotationTemplateItemsEloquent->profit_margin,
            salesperson_id: $quotationTemplateItemsEloquent->salesperson_id
        );
    }

    public static function toEloquent(QuotationTemplateItems $quotationTemplateItems, ?int $sectionId = null, ?int $aowId = null): QuotationTemplateItemsEloquentModel
    {
        $quotationTemplateItemsEloquent = new QuotationTemplateItemsEloquentModel();
        if ($quotationTemplateItems->id) {

            $quotationTemplateItemsEloquent = QuotationTemplateItemsEloquentModel::query()->findOrFail($quotationTemplateItems->id);
            $quotationTemplateItemsEloquent->description = $quotationTemplateItems->description;
            $quotationTemplateItemsEloquent->index = $quotationTemplateItems->index;
            $quotationTemplateItemsEloquent->unit_of_measurement = $quotationTemplateItems->unit_of_measurement;
            $quotationTemplateItemsEloquent->price_with_gst = $quotationTemplateItems->price_with_gst;
            $quotationTemplateItemsEloquent->price_without_gst = $quotationTemplateItems->price_without_gst;
            $quotationTemplateItemsEloquent->cost_price = $quotationTemplateItems->cost_price;
            $quotationTemplateItemsEloquent->profit_margin = $quotationTemplateItems->profit_margin;
        } else {

            $quotationTemplateItemsEloquent->description = $quotationTemplateItems->description;
            $quotationTemplateItemsEloquent->index = $quotationTemplateItems->index;
            $quotationTemplateItemsEloquent->unit_of_measurement = $quotationTemplateItems->unit_of_measurement;
            $quotationTemplateItemsEloquent->quantity = $quotationTemplateItems->quantity;
            $quotationTemplateItemsEloquent->section_id = $sectionId;
            $quotationTemplateItemsEloquent->area_of_work_id = $aowId;
            $quotationTemplateItemsEloquent->price_without_gst = $quotationTemplateItems->price_without_gst;
            $quotationTemplateItemsEloquent->price_with_gst = $quotationTemplateItems->price_with_gst;
            $quotationTemplateItemsEloquent->cost_price = $quotationTemplateItems->cost_price;
            $quotationTemplateItemsEloquent->profit_margin = $quotationTemplateItems->profit_margin;
            $quotationTemplateItemsEloquent->salesperson_id = $quotationTemplateItems->salesperson_id ? $quotationTemplateItems->salesperson_id : null;
        }

        return $quotationTemplateItemsEloquent;
    }
}
