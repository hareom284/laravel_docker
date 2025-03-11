<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

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