<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\EvoItem;
use Src\Company\Document\Infrastructure\EloquentModels\EvoItemEloquentModel;

class EvoItemMapper
{
    public static function fromRequest(array $itemsData, ?int $item_id = null): array
    {
        $items = [];

        foreach ($itemsData as $itemData) {

            $item = new EvoItem(
                id: $item_id,
                template_item_id: $itemData->template_item_id ?? null,
                item_description: $itemData->item_description ?? null,
                unit_rate: $itemData->unit_rate ?? null,
                quantity: $itemData->total_quantity ?? null,
                total: $itemData->total ?? null,
                rooms: $itemData->rooms
            );

            $items[] = $item;
        }

        return $items;
    }

    public static function fromEloquent(EvoItemEloquentModel $itemEloquent): EvoItem
    {
        return new EvoItem(
            id: $itemEloquent->id,
            item_description: $itemEloquent->item_description,
            quantity: $itemEloquent->quantity,
            unit_rate: $itemEloquent->unit_rate,
            total: $itemEloquent->total,
            rooms: $itemEloquent->rooms
        );
    }

    public static function toEloquent(EvoItem $item): EvoItemEloquentModel
    {

        $itemEloquent = new EvoItemEloquentModel();
        if ($item->id) {

            $itemEloquent = EvoItemEloquentModel::query()->findOrFail($item->id);

        }
        $itemEloquent->template_item_id = $item->template_item_id ?? null;
        $itemEloquent->item_description = $item->item_description;
        $itemEloquent->quantity = $item->quantity;
        $itemEloquent->unit_rate = $item->unit_rate;
        $itemEloquent->total = $item->total;
        return $itemEloquent;
    }
}