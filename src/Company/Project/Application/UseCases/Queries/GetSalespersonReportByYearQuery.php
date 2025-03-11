<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class GetSalespersonReportByYearQuery implements QueryInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $salespersonId,
        private readonly int $year
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSalespersonReportByYear($this->salespersonId,$this->year);
    }
}