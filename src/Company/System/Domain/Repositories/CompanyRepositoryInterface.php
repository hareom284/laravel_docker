<?php

namespace Src\Company\System\Domain\Repositories;
use Src\Company\System\Domain\Model\Entities\Company;
use Src\Company\System\Application\DTO\CompanyData;

interface CompanyRepositoryInterface
{
    public function getCompanies($filters = []);

    public function getAll();

    public function findById(int $id);

    public function getDefaultCompany();

    public function store(Company $company): CompanyData;

    public function update(Company $company): Company;

    public function updateDefaultCompany(int $id);

    public function updateAccountingSoftwareCompanyIds(array $data);

    public function delete(int $company_id): void;

    public function increaseQuotationNo(int $company_id);

}
