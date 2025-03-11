<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateRoom;
use Src\Company\Document\Domain\Repositories\EvoTemplateRoomRepositoryInterface;

class UpdateEvoTemplateRoomCommand implements CommandInterface
{
    private EvoTemplateRoomRepositoryInterface $repository;

    public function __construct(
        private readonly EvoTemplateRoom $item
    )
    {
        $this->repository = app()->make(EvoTemplateRoomRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->item);
    }
}