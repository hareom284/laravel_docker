<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\EvoItemRepositoryInterface;

class StoreEvoItemCommand implements CommandInterface
{
    private EvoItemRepositoryInterface $repository;

    public function __construct(
        private readonly array $evoItems,
        private readonly int $evoId
    )
    {
        $this->repository = app()->make(EvoItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->evoItems,$this->evoId);
    }
}