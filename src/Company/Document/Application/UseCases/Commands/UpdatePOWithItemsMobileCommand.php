<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Illuminate\Http\Request;
use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface;

class UpdatePOWithItemsMobileCommand implements CommandInterface
{
    private PurchaseOrderMobileRepositoryInterface $repository;

    public function __construct(
        private readonly Request $purchaseOrder,
        private readonly int $id
    )
    {
        $this->repository = app()->make(PurchaseOrderMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updatePO($this->purchaseOrder,$this->id);
    }
}