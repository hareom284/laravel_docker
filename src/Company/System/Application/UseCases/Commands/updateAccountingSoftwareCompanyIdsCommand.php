<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class updateAccountingSoftwareCompanyIdsCommand implements CommandInterface
{
    private CompanyRepositoryInterface $repository;
    private $companies;

    public function __construct(array $companies)
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
        $this->companies = $companies;
    }

    public function execute(): mixed
    {
        return $this->repository->updateAccountingSoftwareCompanyIds($this->companies);
    }
}