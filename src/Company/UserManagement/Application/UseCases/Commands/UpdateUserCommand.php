<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;


class UpdateUserCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly User $user,
        private readonly array $roleIds,
        private readonly ?Customer $customer = null,
        private readonly ?Staff $staff = null,
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', UserPolicy::class);
        return $this->repository->update($this->user,$this->roleIds,$this->customer,$this->staff);
    }
}
