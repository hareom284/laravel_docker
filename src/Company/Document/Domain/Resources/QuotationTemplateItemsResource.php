<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;

class QuotationTemplateItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'index' => $this->index,
            'price_without_gst' => $this->price_without_gst,
            'price_with_gst' => $this->price_with_gst,
            'cost_price' => $this->cost_price,
            'profit_margin' => $this->profit_margin,
            // 'measurment' => $this->unit_of_measurement,
            'measurment' => $this->unit_of_measurement,
            'is_fixed_measurement' => $this->is_fixed_measurement ?? 0,
            'quantity' => $this->quantity,
            'document_id' => $this->document_id
        ];

        $childItems = QuotationTemplateItemsEloquentModel::where('parent_id', $this->id)->whereNull('document_id')->orderBy('index')->get();

        if(!$childItems->isEmpty())
            $data['items'] = QuotationTemplateItemsResource::collection($childItems);

        return $data;
    }
}
