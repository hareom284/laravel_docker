<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface;

class StoreSupplierDebitCommand implements CommandInterface
{
    private SupplierDebitRepositoryInterface $repository;

    public function __construct(
        private readonly SupplierDebit $supplierDebit,
    )
    {
        $this->repository = app()->make(SupplierDebitRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->supplierDebit);
    }
}