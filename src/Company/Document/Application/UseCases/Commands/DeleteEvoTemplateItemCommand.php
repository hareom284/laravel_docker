<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\EvoTemplateItemRepositoryInterface;

class DeleteEvoTemplateItemCommand implements CommandInterface
{
    private EvoTemplateItemRepositoryInterface $repository;

    public function __construct(
        private readonly int $item_id
    )
    {
        $this->repository = app()->make(EvoTemplateItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->item_id);
    }
}