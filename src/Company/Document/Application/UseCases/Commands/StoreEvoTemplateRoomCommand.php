<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateRoom;
use Src\Company\Document\Domain\Repositories\EvoTemplateRoomRepositoryInterface;

class StoreEvoTemplateRoomCommand implements CommandInterface
{
    private EvoTemplateRoomRepositoryInterface $repository;

    public function __construct(
        private readonly EvoTemplateRoom $room,
    )
    {
        $this->repository = app()->make(EvoTemplateRoomRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->room);
    }
}