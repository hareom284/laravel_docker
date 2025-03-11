<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface;

class UpdateDeviceIdMobileCommand implements CommandInterface
{
    private UserRepositoryMobileInterface $repository;

    public function __construct(
        private readonly array $data,
    )
    {
        $this->repository = app()->make(UserRepositoryMobileInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', UserPolicy::class);
        return $this->repository->updateDeviceId($this->data);
    }
}
