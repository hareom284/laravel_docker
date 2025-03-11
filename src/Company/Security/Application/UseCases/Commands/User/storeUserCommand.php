<?php

namespace Src\Company\Security\Application\UseCases\Commands\User;

use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;
use Src\Company\Security\Domain\Model\User;
use Src\Common\Domain\CommandInterface;


class StoreUserCommand implements CommandInterface
{
    private SecurityRepositoryInterface $repository;

    public function __construct(
        private readonly User $user
    )
    {
        $this->repository = app()->make(SecurityRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->createUser($this->user);
    }
}
