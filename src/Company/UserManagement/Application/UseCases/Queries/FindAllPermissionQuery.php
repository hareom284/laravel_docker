<?php

namespace Src\Company\UserManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\UserManagement\Domain\Repositories\PermissionRepositoryInterface;

class FindAllPermissionQuery implements QueryInterface
{
    private PermissionRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(PermissionRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->permissionsWithoutPagi();
    }
}
