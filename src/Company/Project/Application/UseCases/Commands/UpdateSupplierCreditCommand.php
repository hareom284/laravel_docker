<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SupplierCredit;
use Src\Company\Project\Domain\Repositories\SupplierCreditRepositoryInterface;

class UpdateSupplierCreditCommand implements CommandInterface
{
    private SupplierCreditRepositoryInterface $repository;

    public function __construct(
        private readonly SupplierCredit $supplierCredit,
    )
    {
        $this->repository = app()->make(SupplierCreditRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->supplierCredit);
    }
}