<?php

namespace Src\Company\System\Domain\Repositories;

interface CompanyMobileRepositoryInterface
{

    public function getCompanies($filters = []);

    public function increaseQuotationNo(int $company_id);

}
