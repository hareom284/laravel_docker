<?php
namespace Src\Auth\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;;


class ChangePasswordCommand implements CommandInterface
{
    private AuthRepositoryInterface $repository;

    public function __construct(
        private readonly string $email,
        private readonly Password $password

    )
    {
        $this->repository = app()->make(AuthRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', UserPolicy::class);
        return $this->repository->resetPassword($this->email,$this->password);
    }
}
