<?php
namespace Src\Auth\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;
use Src\Company\UserManagement\Domain\Model\ValueObjects\Password;

class UpdatePasswordCommand implements CommandInterface
{
    private AuthRepositoryInterface $repository;

    public function __construct(
        private readonly int $userId,
        private readonly Password $password

    )
    {
        $this->repository = app()->make(AuthRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', UserPolicy::class);
        return $this->repository->updatePassword($this->userId,$this->password);
    }
}
