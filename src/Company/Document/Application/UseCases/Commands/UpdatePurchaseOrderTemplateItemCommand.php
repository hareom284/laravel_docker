<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrderTemplateItem;
use Src\Company\Document\Domain\Repositories\PurchaseOrderTemplateItemRepositoryInterface;

class UpdatePurchaseOrderTemplateItemCommand implements CommandInterface
{
    private PurchaseOrderTemplateItemRepositoryInterface $repository;

    public function __construct(
        private readonly PurchaseOrderTemplateItem $purchaseOrderTemplateItem
    )
    {
        $this->repository = app()->make(PurchaseOrderTemplateItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->purchaseOrderTemplateItem);
    }
}