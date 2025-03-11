<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateItems;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateItemEloquentModel;

class EvoTemplateItemMapper
{
    public static function fromRequest(Request $request, ?int $item_id = null): EvoTemplateItems
    {
        return new EvoTemplateItems(
            id: $item_id,
            description: $request->string('description'),
            unit_rate_without_gst: $request->float('unit_rate_without_gst'),
            unit_rate_with_gst: $request->float('unit_rate_with_gst'),
        );
    }

    public static function fromEloquent(EvoTemplateItemEloquentModel $itemEloquent): EvoTemplateItems
    {
        return new EvoTemplateItems(
            id: $itemEloquent->id,
            description: $itemEloquent->description,
            unit_rate_without_gst: $itemEloquent->unit_rate_without_gst,
            unit_rate_with_gst: $itemEloquent->unit_rate_with_gst
        );
    }

    public static function toEloquent(EvoTemplateItems $item): EvoTemplateItemEloquentModel
    {

        $itemEloquent = new EvoTemplateItemEloquentModel();
        if ($item->id) {

            $itemEloquent = EvoTemplateItemEloquentModel::query()->findOrFail($item->id);

        }
        $itemEloquent->description = $item->description;
        $itemEloquent->unit_rate_without_gst = $item->unit_rate_without_gst;
        $itemEloquent->unit_rate_with_gst = $item->unit_rate_with_gst;
        return $itemEloquent;
    }
}