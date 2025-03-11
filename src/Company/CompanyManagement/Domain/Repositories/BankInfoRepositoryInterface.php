<?php

namespace Src\Company\CompanyManagement\Domain\Repositories;

interface BankInfoRepositoryInterface
{
    public function getAllBankInfos();

    public function syncWithAccountingSoftwareData(int $companyId);
}
