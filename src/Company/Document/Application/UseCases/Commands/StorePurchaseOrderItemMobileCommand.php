<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderItemMobileRepositoryInterface;

class StorePurchaseOrderItemMobileCommand implements CommandInterface
{
    private PurchaseOrderItemMobileRepositoryInterface $repository;

    public function __construct(
        private readonly array $items,
        private readonly int $poId
    )
    {
        $this->repository = app()->make(PurchaseOrderItemMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->items,$this->poId);
    }
}