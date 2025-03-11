<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Company\UserManagement\Domain\Model\User;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;
use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\StaffManagement\Domain\Model\Staff;

class StoreUserCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly User $user,
        private readonly mixed $password,
        private readonly array $roleIds,
        private readonly ?array $salespersonIds,
        private readonly ?Customer $customer = null,
        private readonly ?Staff $staff = null,
    ) {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function execute(): User
    {

        return $this->repository->store($this->user, $this->password, $this->roleIds, $this->salespersonIds, $this->customer, $this->staff);
    }
}
