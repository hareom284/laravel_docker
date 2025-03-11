<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\SaleReport;
use Src\Company\Project\Domain\Repositories\SaleReportMobileRepositoryInterface;

class UpdateSaleReportMobileCommand implements CommandInterface
{
    private SaleReportMobileRepositoryInterface $repository;

    public function __construct(
        private readonly SaleReport $saleReport,
        private readonly ?array $saleCommissions
    )
    {
        $this->repository = app()->make(SaleReportMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->update($this->saleReport, $this->saleCommissions);
    }
}