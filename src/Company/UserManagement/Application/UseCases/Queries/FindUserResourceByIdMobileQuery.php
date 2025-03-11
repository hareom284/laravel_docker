<?php

namespace Src\Company\UserManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface;

class FindUserResourceByIdMobileQuery implements QueryInterface
{
    private UserRepositoryMobileInterface $repository;

    public function __construct(
        private readonly string $id,
    )
    {
        $this->repository = app()->make(UserRepositoryMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->findUserInfoById($this->id);
    }
}
