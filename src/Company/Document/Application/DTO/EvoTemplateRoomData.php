<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateRoomEloquentModel;

class EvoTemplateRoomData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $room_name
    )
    {}

    public static function fromRequest(Request $request, ?int $room_id = null): EvoTemplateRoomData
    {
        return new self(
            id: $room_id,
            room_name: $request->string('room_name')
        );
    }

    public static function fromEloquent(EvoTemplateRoomEloquentModel $roomEloquent): self
    {
        return new self(
            id: $roomEloquent->id,
            room_name: $roomEloquent->room_name
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'room_name' => $this->room_name        ];
    }
}