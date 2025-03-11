<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Policies\UserPolicy;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class DeleteCompanyCommand implements CommandInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct(
        private readonly int $company_id
    )
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteCompany', UserPolicy::class);
        return $this->repository->delete($this->company_id);
    }
}
