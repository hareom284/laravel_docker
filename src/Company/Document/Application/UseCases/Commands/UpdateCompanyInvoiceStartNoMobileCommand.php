<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;

class UpdateCompanyInvoiceStartNoMobileCommand implements CommandInterface
{
    private RenovationDocumentMobileInterface $repository;

    public function __construct(
        private readonly int $company_id,
        private readonly int $invoice_no_start,
    )
    {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateInvoiceStartNumber($this->company_id,$this->invoice_no_start);
    }
}