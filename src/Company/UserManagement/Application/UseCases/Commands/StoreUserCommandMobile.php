<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Company\UserManagement\Domain\Model\User;
use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\StaffManagement\Domain\Model\Staff;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface;

class StoreUserCommandMobile implements CommandInterface
{
    private UserRepositoryMobileInterface $repository;

    public function __construct(
        private readonly User $user,
        private readonly mixed $password,
        private readonly array $roleIds,
        private readonly ?array $salespersonIds,
        private readonly ?Customer $customer = null,
        private readonly ?Staff $staff = null,
    ) {
        $this->repository = app()->make(UserRepositoryMobileInterface::class);
    }

    public function execute(): User
    {

        return $this->repository->store($this->user, $this->password, $this->roleIds, $this->salespersonIds, $this->customer, $this->staff);
    }
}
