<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\Mappers\PurchaseOrderItemMapper;
use Src\Company\Document\Domain\Repositories\PurchaseOrderItemRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderItemEloquentModel;
use Src\Company\Document\Domain\Resources\PurchaseOrderItemResource;

class PurchaseOrderItemRepository implements PurchaseOrderItemRepositoryInterface
{
    public function getAllItems(int $id)
    {
        //purchase order item lists

        $itemEloquent = PurchaseOrderItemEloquentModel::where('purchase_order_id',$id)->get();

        $items = PurchaseOrderItemResource::collection($itemEloquent);
        
        return $items;
    }

    public function store(array $items,$poId): array
    {        
        return DB::transaction(function () use ($items,$poId) {

            $itemEloquents = [];

            foreach($items as $item)
            {
                $itemEloquent = PurchaseOrderItemMapper::toEloquent($item);

                $itemEloquent->purchase_order_id = $poId;

                $itemEloquent->save();

                $itemEloquents[] = $itemEloquent;
            }

            return $itemEloquents;
        });
    }

    public function delete(int $item_id): void
    {
        $itemEloquent = PurchaseOrderItemEloquentModel::query()->findOrFail($item_id);

        $itemEloquent->delete();
    }

}