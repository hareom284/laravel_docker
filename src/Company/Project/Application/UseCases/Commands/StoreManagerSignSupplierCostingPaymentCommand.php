<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Document\Domain\Repositories\ContractRepositoryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCostingPaymentRepositoryInterface;

class StoreManagerSignSupplierCostingPaymentCommand implements CommandInterface
{
    private SupplierCostingPaymentRepositoryInterface $repository;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(SupplierCostingPaymentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        return $this->repository->managerSign($this->request);
    }
}