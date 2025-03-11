<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderTemplateItemRepositoryInterface;

class DeletePurchaseOrderTemplateItem implements CommandInterface
{
    private PurchaseOrderTemplateItemRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(PurchaseOrderTemplateItemRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->id);
    }
}