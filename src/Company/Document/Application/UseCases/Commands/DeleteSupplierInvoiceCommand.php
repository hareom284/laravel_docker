<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\SupplierInvoiceRepositoryInterface;

class DeleteSupplierInvoiceCommand implements CommandInterface
{
    private SupplierInvoiceRepositoryInterface $repository;

    public function __construct(
        private readonly string $id,
    )
    {
        $this->repository = app()->make(SupplierInvoiceRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->delete($this->id);
    }
}