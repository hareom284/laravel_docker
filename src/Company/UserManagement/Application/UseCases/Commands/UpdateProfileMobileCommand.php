<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;

class UpdateProfileMobileCommand implements CommandInterface
{
    private UserRepositoryMobileInterface $repository;

    public function __construct(
        private readonly array $user,
        private readonly ?Password $password,
        private readonly int $id
    )
    {
        $this->repository = app()->make(UserRepositoryMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateProfile($this->user,$this->password,$this->id);
    }
}
