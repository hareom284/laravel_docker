<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\EvoTemplateItemData;
use Src\Company\Document\Application\DTO\EvoTemplateRoomData;
use Src\Company\Document\Application\Mappers\EvoTemplateRoomMapper;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateRoom;
use Src\Company\Document\Domain\Repositories\EvoTemplateRoomRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateRoomEloquentModel;
use Src\Company\Document\Domain\Resources\EvoTemplateRoomResource;

class EvoTemplateRoomRepository implements EvoTemplateRoomRepositoryInterface
{
    public function getAllRooms()
    {
        //evo template item lists

        $roomEloquent = EvoTemplateRoomEloquentModel::all();

        $rooms = EvoTemplateRoomResource::collection($roomEloquent);
        
        return $rooms;
    }

    public function store(EvoTemplateRoom $room): EvoTemplateRoomData
    {
        return DB::transaction(function () use ($room) {

            $roomEloquent = EvoTemplateRoomMapper::toEloquent($room);

            $roomEloquent->save();

            return EvoTemplateRoomData::fromEloquent($roomEloquent);
        });
    }

    public function update(EvoTemplateRoom $room): EvoTemplateRoomData
    {
        return DB::transaction(function () use ($room) {

            $roomEloquent = EvoTemplateRoomMapper::toEloquent($room);

            $roomEloquent->save();

            return EvoTemplateRoomData::fromEloquent($roomEloquent);
        });
    }

    public function delete(int $room_id): void
    {
        $roomEloquent = EvoTemplateRoomEloquentModel::query()->findOrFail($room_id);

        $roomEloquent->delete();
    }

}