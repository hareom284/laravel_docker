<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface;

class FindPurchaseOrderByIdMobileQuery implements QueryInterface
{
    private PurchaseOrderMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(PurchaseOrderMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findById($this->id);
    }
}