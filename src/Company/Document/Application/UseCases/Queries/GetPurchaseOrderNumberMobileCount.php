<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface;

class GetPurchaseOrderNumberMobileCount implements QueryInterface
{
    private PurchaseOrderMobileRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(PurchaseOrderMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getPurchaseOrderNumberCount();
    }
}