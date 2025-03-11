<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\EvoTemplateRoomData;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateRoom;

interface EvoTemplateRoomRepositoryInterface
{
    public function getAllRooms();

    public function store(EvoTemplateRoom $rooms): EvoTemplateRoomData;

    public function update(EvoTemplateRoom $rooms): EvoTemplateRoomData;

    public function delete(int $room_id): void;

}
