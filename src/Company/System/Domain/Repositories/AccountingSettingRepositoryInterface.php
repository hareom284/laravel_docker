<?php

namespace Src\Company\System\Domain\Repositories;

interface AccountingSettingRepositoryInterface
{
    public function findByCompany($companyId);

    public function update(array $settings);
}
