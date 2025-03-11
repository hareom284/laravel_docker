<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrderItem;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderItemEloquentModel;

class PurchaseOrderItemMapper
{
    public static function fromRequest(array $itemsData, ?int $item_id = null): array
    {
        $items = [];

        foreach ($itemsData as $itemData) {

            $item = new PurchaseOrderItem(
                id: $item_id,
                description: $itemData->description ?? null,
                code: $itemData->code ?? null,
                quantity: $itemData->quantity ?? null,
                size: $itemData->size ?? null,
            );

            $items[] = $item;
        }

        return $items;
    }

    public static function fromEloquent(PurchaseOrderItemEloquentModel $itemEloquent): PurchaseOrderItem
    {
        return new PurchaseOrderItem(
            id: $itemEloquent->id,
            description: $itemEloquent->description,
            code: $itemEloquent->code,
            quantity: $itemEloquent->quantity,
            size: $itemEloquent->size
        );
    }

    public static function toEloquent(PurchaseOrderItem $item): PurchaseOrderItemEloquentModel
    {

        $itemEloquent = new PurchaseOrderItemEloquentModel();
        if ($item->id) {

            $itemEloquent = PurchaseOrderItemEloquentModel::query()->findOrFail($item->id);

        }
        $itemEloquent->description = $item->description;
        $itemEloquent->quantity = $item->quantity;
        $itemEloquent->code = $item->code;
        $itemEloquent->size = $item->size;
        return $itemEloquent;
    }
}