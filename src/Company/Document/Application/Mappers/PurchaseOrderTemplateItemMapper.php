<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrderTemplateItem;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderTemplateItemEloquentModel;

class PurchaseOrderTemplateItemMapper
{
    public static function fromRequest(Request $request, ?int $id = null): PurchaseOrderTemplateItem
    {
        return new PurchaseOrderTemplateItem(
            id: $id,
            description: $request->string('description'),
            code: $request->filled('code') ? $request->string('code') : null,
            size: $request->filled('size') ? $request->string('size') : null,
            quantity: $request->filled('quantity') ? $request->string('quantity') : null,
            vendor_category_id: $request->integer('vendor_category_id'),
            company_id: $request->integer('company_id'),
        );
    }

    public static function fromEloquent(PurchaseOrderTemplateItemEloquentModel $poTemplateItemEloquent): PurchaseOrderTemplateItem
    {
        return new PurchaseOrderTemplateItem(
            id: $poTemplateItemEloquent->id,
            description: $poTemplateItemEloquent->poTemplateItemEloquent,
            code: $poTemplateItemEloquent->code,
            quantity: $poTemplateItemEloquent->quantity,
            size: $poTemplateItemEloquent->size,
            vendor_category_id: $poTemplateItemEloquent->vendor_category_id,
            company_id: $poTemplateItemEloquent->company_id,
        );
    }

    public static function toEloquent(PurchaseOrderTemplateItem $purchaseOrderTemplateItem): PurchaseOrderTemplateItemEloquentModel
    {
        $poTemplateItemEloquent = new PurchaseOrderTemplateItemEloquentModel();

        if ($purchaseOrderTemplateItem->id) {
            $poTemplateItemEloquent = PurchaseOrderTemplateItemEloquentModel::query()->findOrFail($purchaseOrderTemplateItem->id);
        }

        $poTemplateItemEloquent->description = $purchaseOrderTemplateItem->description;
        $poTemplateItemEloquent->code = $purchaseOrderTemplateItem->code;
        $poTemplateItemEloquent->size = $purchaseOrderTemplateItem->size;
        $poTemplateItemEloquent->quantity = $purchaseOrderTemplateItem->quantity;
        $poTemplateItemEloquent->vendor_category_id = $purchaseOrderTemplateItem->vendor_category_id;
        $poTemplateItemEloquent->company_id = $purchaseOrderTemplateItem->company_id;
        
        return $poTemplateItemEloquent;
    }
}