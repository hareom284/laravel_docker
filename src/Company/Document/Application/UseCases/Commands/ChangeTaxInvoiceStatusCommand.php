<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\TaxInvoiceRepositoryInterface;

class ChangeTaxInvoiceStatusCommand implements CommandInterface
{
    private TaxInvoiceRepositoryInterface $repository;

    public function __construct(
        private readonly int $taxId,
        private readonly int $status
    )
    {
        $this->repository = app()->make(TaxInvoiceRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->changeStatus($this->taxId, $this->status);
    }
}