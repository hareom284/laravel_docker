<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SupplierDebit;
use Src\Company\Project\Domain\Model\Entities\VendorInvoiceExpenseType;
use Src\Company\Project\Domain\Repositories\VendorInvoiceExpenseTypeRepositoryInterface;

class UpdateVendorInvoiceExpenseTypeCommand implements CommandInterface
{
    private VendorInvoiceExpenseTypeRepositoryInterface $repository;

    public function __construct(
        private readonly VendorInvoiceExpenseType $vendorInvoiceExpenseType,
    )
    {
        $this->repository = app()->make(VendorInvoiceExpenseTypeRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->vendorInvoiceExpenseType);
    }
}