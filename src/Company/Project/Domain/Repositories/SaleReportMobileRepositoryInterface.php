<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\SaleReportData;
use Src\Company\Project\Domain\Model\Entities\SaleReport;

interface SaleReportMobileRepositoryInterface
{
    public function getSalepersonKpiReportMonth($salespersonUserId, $month = null, $year = null);

    public function store(int $projetId);

    public function update(SaleReport $saleReport, $saleCommissions): SaleReportData;

}