<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\EvoTemplateItems;
use Src\Company\Document\Domain\Repositories\EvoTemplateItemRepositoryInterface;

class StoreEvoTemplateItemCommand implements CommandInterface
{
    private EvoTemplateItemRepositoryInterface $repository;

    public function __construct(
        private readonly EvoTemplateItems $item,
    )
    {
        $this->repository = app()->make(EvoTemplateItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->item);
    }
}