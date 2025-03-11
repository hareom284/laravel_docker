<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

class UpdateCustomerCommand implements CommandInterface
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
        return $this->repository->customerUpdate($this->id,$this->user,$this->password);
    }
}
