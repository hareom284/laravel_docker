<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;

class UpdateCustomerUserCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
        private readonly mixed $user,
        private readonly mixed $password
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', UserPolicy::class);
        return $this->repository->updateCustomerUser($this->id,$this->user,$this->password);
    }
}
