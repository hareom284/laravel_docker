<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderTemplateItemRepositoryInterface;

class FindAllPurchaseOrderTemplateItemQuery implements QueryInterface
{
    private PurchaseOrderTemplateItemRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(PurchaseOrderTemplateItemRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->index($this->filters);
    }
}