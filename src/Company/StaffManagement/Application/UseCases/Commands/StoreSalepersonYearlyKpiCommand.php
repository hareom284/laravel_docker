<?php

namespace Src\Company\StaffManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\StaffManagement\Domain\Model\Entities\SalepersonYearlyKpi;
use Src\Company\StaffManagement\Domain\Repositories\SalepersonYearlyKpiRepositoryInterface;

class StoreSalepersonYearlyKpiCommand implements CommandInterface
{
    private SalepersonYearlyKpiRepositoryInterface $repository;

    public function __construct(
        private readonly SalepersonYearlyKpi $salepersonKpi
    )
    {
        $this->repository = app()->make(SalepersonYearlyKpiRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->store($this->salepersonKpi);
    }
}
