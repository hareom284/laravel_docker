<?php

namespace Src\Company\Project\Domain\Repositories;

interface CustomerPaymentMobileRepositoryInterface
{
    public function getBySaleReportId($saleReportId);
}