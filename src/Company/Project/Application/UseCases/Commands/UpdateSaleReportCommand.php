<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SaleReport;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class UpdateSaleReportCommand implements CommandInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly SaleReport $saleReport,
        private readonly ?array $saleCommissions
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->saleReport, $this->saleCommissions);
    }
}