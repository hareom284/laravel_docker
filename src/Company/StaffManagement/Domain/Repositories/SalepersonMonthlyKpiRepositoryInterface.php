<?php

namespace Src\Company\StaffManagement\Domain\Repositories;

use Src\Company\StaffManagement\Application\DTO\SalepersonMonthlyKpiData;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonMonthlyKpi;

interface SalepersonMonthlyKpiRepositoryInterface
{
    public function getKpiRecords($saleperson_id);

    public function getKpiRecordsByMonth($saleperson_id,$year,$month);

    public function store(SalepersonMonthlyKpi $salepersonKpi): SalepersonMonthlyKpiData;

}
