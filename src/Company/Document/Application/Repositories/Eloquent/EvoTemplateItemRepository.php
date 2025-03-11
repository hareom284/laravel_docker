<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\EvoTemplateItemData;
use Src\Company\Document\Application\Mappers\EvoTemplateItemMapper;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateItems;
use Src\Company\Document\Domain\Repositories\EvoTemplateItemRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateItemEloquentModel;
use Src\Company\Document\Domain\Resources\EvoTemplateItemResource;

class EvoTemplateItemRepository implements EvoTemplateItemRepositoryInterface
{
    public function getAllItems()
    {
        //evo template item lists

        $itemEloquent = EvoTemplateItemEloquentModel::all();

        $items = EvoTemplateItemResource::collection($itemEloquent);
        
        return $items;
    }

    public function store(EvoTemplateItems $item): EvoTemplateItemData
    {
        return DB::transaction(function () use ($item) {

            $itemEloquent = EvoTemplateItemMapper::toEloquent($item);

                $itemEloquent->save();

            return EvoTemplateItemData::fromEloquent($itemEloquent);
        });
    }

    public function update(EvoTemplateItems $item): EvoTemplateItemData
    {
        return DB::transaction(function () use ($item) {

            $itemEloquent = EvoTemplateItemMapper::toEloquent($item);

            $itemEloquent->save();

            return EvoTemplateItemData::fromEloquent($itemEloquent);
        });
    }

    public function delete(int $item_id): void
    {
        $itemEloquent = EvoTemplateItemEloquentModel::query()->findOrFail($item_id);
        
        $itemEloquent->delete();
    }

}