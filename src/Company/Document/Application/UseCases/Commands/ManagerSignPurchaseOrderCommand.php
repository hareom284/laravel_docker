<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;

class ManagerSignPurchaseOrderCommand implements CommandInterface
{
    private PurchaseOrderRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(PurchaseOrderRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->managerSign($this->request);
    }
}