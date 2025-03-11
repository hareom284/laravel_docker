<?php

namespace Src\Company\StaffManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\StaffManagement\Domain\Repositories\StaffRepositoryInterface;

class StoreStaffCommand implements CommandInterface
{
    private StaffRepositoryInterface $repository;

    public function __construct(
        private readonly Staff $staff,
    )
    {
        $this->repository = app()->make(StaffRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeRole', UserPolicy::class);
        return $this->repository->store($this->staff);
    }
}
