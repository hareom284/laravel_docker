<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Policies\UserPolicy;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

class DeleteUserCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly int $user_id
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', UserPolicy::class);
        return $this->repository->delete($this->user_id);
    }
}
