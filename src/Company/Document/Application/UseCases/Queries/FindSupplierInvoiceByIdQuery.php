<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\SupplierInvoiceRepositoryInterface;

class FindSupplierInvoiceByIdQuery implements QueryInterface
{
    private SupplierInvoiceRepositoryInterface $repository;

    public function __construct(
        private readonly string $id,
    )
    {
        $this->repository = app()->make(SupplierInvoiceRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->show($this->id);
    }
}