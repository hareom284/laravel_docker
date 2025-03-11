<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\TaxInvoiceRepositoryInterface;

class GetTaxInvoicesByStatusOrder implements QueryInterface
{
    private TaxInvoiceRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(TaxInvoiceRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getListByStatusOrder($this->filters);
    }
}