<?php

namespace Src\Auth\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Auth\Domain\Repositories\AuthRepositoryInterface;

class FindUserByEmailQuery implements QueryInterface
{
    private AuthRepositoryInterface $repository;

    public function __construct(
        private readonly string $email,
    )
    {
        $this->repository = app()->make(AuthRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findUserByEmail($this->email);
    }
}