<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface;

class FindPurchaseOrderByProjectIdMobileQuery implements QueryInterface
{
    private PurchaseOrderMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
        private readonly bool $isCount
    )
    {
        $this->repository = app()->make(PurchaseOrderMobileRepositoryInterface::class);
    }

    public function handle()
    {
        $poLists = $this->repository->getPurchaseOrderByProjectId($this->projectId);

        if($this->isCount){

            return count($poLists);

        }else{
            return $poLists;
        }
    }
}