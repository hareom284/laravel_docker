<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Evo;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;

class StoreEvoCommand implements CommandInterface
{
    private EvoRepositoryInterface $repository;

    public function __construct(
        private readonly Evo $evo,
    )
    {
        $this->repository = app()->make(EvoRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->evo);
    }
}