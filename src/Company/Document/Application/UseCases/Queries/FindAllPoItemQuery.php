<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderItemRepositoryInterface;

class FindAllPoItemQuery implements QueryInterface
{
    private PurchaseOrderItemRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(PurchaseOrderItemRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAllItems($this->id);
    }
}