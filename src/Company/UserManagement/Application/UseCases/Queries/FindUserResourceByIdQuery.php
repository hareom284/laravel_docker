<?php

namespace Src\Company\UserManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;

class FindUserResourceByIdQuery implements QueryInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly string $id,
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findUserInfoById($this->id);
    }
}
