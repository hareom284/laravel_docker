<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface;

class FindAllPurchaseOrderMobileQuery implements QueryInterface
{
    private PurchaseOrderMobileRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(PurchaseOrderMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllPurchaseOrders($this->filters);
    }
}