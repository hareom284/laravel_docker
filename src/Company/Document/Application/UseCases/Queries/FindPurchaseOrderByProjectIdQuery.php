<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class FindPurchaseOrderByProjectIdQuery implements QueryInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
        private readonly bool $isCount
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
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