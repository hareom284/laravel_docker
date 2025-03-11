<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateRoom;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateRoomEloquentModel;

class EvoTemplateRoomMapper
{
    public static function fromRequest(Request $request, ?int $room_id = null): EvoTemplateRoom
    {
        return new EvoTemplateRoom(
            id: $room_id,
            room_name: $request->string('room_name')
        );
    }

    public static function fromEloquent(EvoTemplateRoomEloquentModel $roomEloquent): EvoTemplateRoom
    {
        return new EvoTemplateRoom(

            id: $roomEloquent->id,
            
            room_name: $roomEloquent->room_name,
        );
    }

    public static function toEloquent(EvoTemplateRoom $room): EvoTemplateRoomEloquentModel
    {
        $roomEloquent = new EvoTemplateRoomEloquentModel();

        if ($room->id) {

            $roomEloquent = EvoTemplateRoomEloquentModel::query()->findOrFail($room->id);

        }
        $roomEloquent->room_name = $room->room_name;

        return $roomEloquent;
    }
}