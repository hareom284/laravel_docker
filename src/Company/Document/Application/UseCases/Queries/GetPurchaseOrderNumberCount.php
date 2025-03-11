<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class GetPurchaseOrderNumberCount implements QueryInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getPurchaseOrderNumberCount();
    }
}