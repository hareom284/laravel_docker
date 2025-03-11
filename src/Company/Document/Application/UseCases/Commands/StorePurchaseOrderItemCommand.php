<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderItemRepositoryInterface;

class StorePurchaseOrderItemCommand implements CommandInterface
{
    private PurchaseOrderItemRepositoryInterface $repository;

    public function __construct(
        private readonly array $items,
        private readonly int $poId
    )
    {
        $this->repository = app()->make(PurchaseOrderItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->items,$this->poId);
    }
}