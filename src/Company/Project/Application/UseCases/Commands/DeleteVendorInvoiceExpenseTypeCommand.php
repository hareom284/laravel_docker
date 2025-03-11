<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\VendorInvoiceExpenseTypeRepositoryInterface;

class DeleteVendorInvoiceExpenseTypeCommand implements CommandInterface
{
    private VendorInvoiceExpenseTypeRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(VendorInvoiceExpenseTypeRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->destroy($this->id);
    }
}