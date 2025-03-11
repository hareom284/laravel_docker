<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\EvoTemplateRoomRepositoryInterface;

class DeleteEvoTemplateRoomCommand implements CommandInterface
{
    private EvoTemplateRoomRepositoryInterface $repository;

    public function __construct(
        private readonly int $room_id
    )
    {
        $this->repository = app()->make(EvoTemplateRoomRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->room_id);
    }
}