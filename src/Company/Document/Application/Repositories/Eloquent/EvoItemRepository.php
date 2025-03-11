<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\EvoItemData;
use Src\Company\Document\Application\Mappers\EvoItemMapper;
use Src\Company\Document\Domain\Model\Entities\EvoItem;
use Src\Company\Document\Domain\Repositories\EvoItemRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\EvoItemEloquentModel;

class EvoItemRepository implements EvoItemRepositoryInterface
{

    public function store(array $evoItems,$evoId): array
    {        
        return DB::transaction(function () use ($evoItems,$evoId) {

            $itemEloquents = [];

            foreach($evoItems as $item)
            {
                $itemEloquent = EvoItemMapper::toEloquent($item);

                $itemEloquent->evo_id = $evoId;

                $itemEloquent->save();

                // Assuming 'rooms' is a relationship method in the EvoItem model
                foreach ($item->rooms as $room) {

                    $itemEloquent->rooms()->attach($room->room_id, ['quantity' => $room->quantity, 'name' => $room->room_name]);
                }


                $itemEloquents[] = $itemEloquent;
            }

            return $itemEloquents;
        });
    }

    public function delete(int $item_id): void
    {
        $itemEloquent = EvoItemEloquentModel::query()->findOrFail($item_id);
        $itemEloquent->delete();
    }

}