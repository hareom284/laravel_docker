<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SupplierCreditRepositoryInterface;

class GetSupplierCreditReportQuery implements QueryInterface
{
    private SupplierCreditRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(SupplierCreditRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getReport($this->filters);
    }
}