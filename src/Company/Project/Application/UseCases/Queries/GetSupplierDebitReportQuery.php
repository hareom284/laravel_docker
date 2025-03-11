<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierDebitRepositoryInterface;

class GetSupplierDebitReportQuery implements QueryInterface
{
    private SupplierDebitRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(SupplierDebitRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getReport($this->filters);
    }
}