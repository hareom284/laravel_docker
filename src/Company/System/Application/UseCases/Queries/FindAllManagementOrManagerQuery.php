<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

class FindAllManagementOrManagerQuery implements QueryInterface
{
    private UserRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getManagementOrManger();
    }
}