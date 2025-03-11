<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class StorePurchaseOrderCommand implements CommandInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private readonly PurchaseOrder $po
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->po);
    }
}