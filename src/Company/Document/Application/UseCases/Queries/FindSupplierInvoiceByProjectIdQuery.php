<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\SupplierInvoiceRepositoryInterface;

class FindSupplierInvoiceByProjectIdQuery implements QueryInterface
{
    private SupplierInvoiceRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(SupplierInvoiceRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSupplierInvoices($this->projectId);
    }
}