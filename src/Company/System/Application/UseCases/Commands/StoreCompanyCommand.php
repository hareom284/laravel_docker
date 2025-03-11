<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Model\Entities\Company;
use Src\Company\UserManagement\Domain\Policies\UserPolicy;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class StoreCompanyCommand implements CommandInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct(
        private readonly Company $company
    )
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->store($this->company);
    }
}
