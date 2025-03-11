<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\TaxInvoiceMobileRepositoryInterface;

class FindTaxInvoiceByProjectIdMobileQuery implements QueryInterface
{
    private TaxInvoiceMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(TaxInvoiceMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->findTaxByProjectId($this->id);
    }
}