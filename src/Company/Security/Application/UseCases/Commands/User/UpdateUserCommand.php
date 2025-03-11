<?php

namespace Src\Company\Security\Application\UseCases\Commands\User;


use Src\Common\Domain\CommandInterface;
use Src\Company\Security\Application\DTO\UserData;
use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;

class UpdateUserCommand implements CommandInterface
{
    private SecurityRepositoryInterface $repository;

    public function __construct(
        private readonly UserData $userData
    )
    {
        $this->repository = app()->make(SecurityRepositoryInterface::class);
    }

    public function execute()
    {
        return $this->repository->updateUser($this->userData);
    }
}
