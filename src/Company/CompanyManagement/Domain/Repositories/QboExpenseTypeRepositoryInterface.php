<?php

namespace Src\Company\CompanyManagement\Domain\Repositories;

interface QboExpenseTypeRepositoryInterface
{
    public function syncWithAccountingSoftwareData($companyId);

    public function getAllExpenseTypes();
}
