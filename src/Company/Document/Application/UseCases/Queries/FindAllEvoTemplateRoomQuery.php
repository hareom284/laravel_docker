<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\EvoTemplateRoomData;
use Src\Company\Document\Domain\Repositories\EvoTemplateRoomRepositoryInterface;

class FindAllEvoTemplateRoomQuery implements QueryInterface
{
    private EvoTemplateRoomRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(EvoTemplateRoomRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllRooms();
    }
}