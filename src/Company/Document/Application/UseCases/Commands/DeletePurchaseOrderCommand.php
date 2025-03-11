<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class DeletePurchaseOrderCommand implements CommandInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private readonly int $po_id
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->po_id);
    }
}