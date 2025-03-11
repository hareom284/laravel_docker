<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\CustomerPaymentData;
use Src\Company\Project\Domain\Model\Entities\CustomerPayment;

interface ProjectReportRepositoryInterface
{
    public function getProjectReport(int $projectId);
}