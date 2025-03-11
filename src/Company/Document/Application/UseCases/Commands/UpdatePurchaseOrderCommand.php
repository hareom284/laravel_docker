<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Illuminate\Http\Request;
use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class UpdatePurchaseOrderCommand implements CommandInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private readonly Request $purchaseOrder,
        private readonly int $id
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->purchaseOrder,$this->id);
    }
}