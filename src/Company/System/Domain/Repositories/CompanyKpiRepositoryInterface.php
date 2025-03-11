<?php

namespace Src\Company\System\Domain\Repositories;
use Src\Company\System\Domain\Model\Entities\Company;
use Src\Company\System\Application\DTO\CompanyData;
use Src\Company\System\Application\DTO\CompanyKpiData;
use Src\Company\System\Domain\Model\Entities\CompanyKpi;

interface CompanyKpiRepositoryInterface
{
    public function getKpiRecords($company_id);

    public function getKpiRecordsByYear($company_id,$year);

    public function store(CompanyKpi $companyKpi): CompanyKpiData;

}
