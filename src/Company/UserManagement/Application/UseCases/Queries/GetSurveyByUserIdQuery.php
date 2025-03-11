<?php

namespace Src\Company\UserManagement\Application\UseCases\Queries;


use Src\Common\Domain\QueryInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryInterface;

class GetSurveyByUserIdQuery implements QueryInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getSurveyByUserId($this->id);
    }
}
