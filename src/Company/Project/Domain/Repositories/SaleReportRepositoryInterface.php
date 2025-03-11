<?php

namespace Src\Company\Project\Domain\Repositories;

use Illuminate\Http\Request;
use Src\Company\Project\Application\DTO\SaleReportData;
use Src\Company\Project\Domain\Model\Entities\SaleReport;

interface SaleReportRepositoryInterface
{
    public function getSaleReportByProjectId($projectId);

    public function getSaleReportByYear($companId,$year,$startDate,$endDate);

    public function getSaleReportByMonth($companId,$year,$month,$startDate,$endDate);

    public function getSalespersonReportByYear($salespersonId,$year);
    
    public function getSalespersonReportByMonth($salespersonId,$year,$month);

    public function getSalepersonKpiReportMonth($salespersonId,$year,$month);

    public function getSalepersonKpiReportYear($salespersonId,$year);

    public function salepersonSaleReportWithKpiInYear($salepersonId,$year);

    public function companySaleReportWithKpiInYear($companyId,$year);

    public function store(int $projectId);

    public function update(SaleReport $saleReport, $saleCommissions): SaleReportData;

    public function destroy(int $sale_report_id): void;

    public function getPendingApprovalDocuments(array $filters);

    public function getManagerPendingApprovalDocuments(array $filters);

    public function markedSaleReport(int $sale_report_id, Request $request);

    public function signSaleReport($id, $data);

}