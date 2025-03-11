<?php

namespace Src\Company\StaffManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonMonthlyKpi;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonMonthlyKpiRepositoryInterface;

class StoreSalepersonMonthlyKpiCommand implements CommandInterface
{
    private SalepersonMonthlyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly SalepersonMonthlyKpi $salepersonKpi
    )
    {
        $this->repository = app()->make(SalepersonMonthlyKpiRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->store($this->salepersonKpi);
    }
}
