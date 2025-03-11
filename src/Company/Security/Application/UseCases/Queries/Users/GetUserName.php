<?php

namespace Src\Company\Security\Application\UseCases\Queries\Users;


use Src\Company\Security\Domain\Repositories\SecurityRepositoryInterface;

use Src\Common\Domain\QueryInterface;

class GetUserName implements QueryInterface
{
    private SecurityRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(SecurityRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getUsersName();
    }
}
