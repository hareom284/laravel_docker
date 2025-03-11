<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderTemplateItemRepositoryInterface;

class GetPurchaseOrderTemplateItemsForPOCreateQuery implements QueryInterface
{
    private PurchaseOrderTemplateItemRepositoryInterface $repository;

    public function __construct(
        private readonly int $companyId,
        private readonly int $vendorCategoryId
    )
    {
        $this->repository = app()->make(PurchaseOrderTemplateItemRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getItemsForPoCreate($this->companyId,$this->vendorCategoryId);
    }
}