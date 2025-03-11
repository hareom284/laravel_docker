<?php

namespace Src\Company\Project\Domain\Repositories;

interface SupplierCostingMobileRepositoryInterface
{
    public function index(array $filters);

    public function getByProjectId($projectId);


}
