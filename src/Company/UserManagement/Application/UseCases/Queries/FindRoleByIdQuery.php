<?php

namespace Src\Company\UserManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\UserManagement\Domain\Repositories\RoleRepositoryInterface;

class FindRoleByIdQuery implements QueryInterface
{
    private RoleRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(RoleRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findById($this->id);
    }
}
