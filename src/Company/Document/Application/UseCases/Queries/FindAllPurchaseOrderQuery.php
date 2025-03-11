<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class FindAllPurchaseOrderQuery implements QueryInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllPurchaseOrders($this->filters);
    }
}