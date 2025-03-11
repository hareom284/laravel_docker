<?php

namespace Src\Company\StaffManagement\Domain\Repositories;
use Src\Company\StaffManagement\Application\DTO\SalepersonYearlyKpiData;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonYearlyKpi;

interface SalepersonYearlyKpiRepositoryInterface
{
    public function getKpiRecords($saleperson_id);

    public function getKpiRecordsByYear($saleperson_id,$year);

    public function store(SalepersonYearlyKpi $salepersonKpi): SalepersonYearlyKpiData;

}
