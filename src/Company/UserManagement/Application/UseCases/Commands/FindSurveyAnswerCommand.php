<?php

namespace Src\Company\UserManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\UserManagement\Domain\Repositories\UserRepositoryMobileInterface;

class FindSurveyAnswerCommand implements CommandInterface
{
    private UserRepositoryMobileInterface $repository;

    public function __construct(
    )
    {
        $this->repository = app()->make(UserRepositoryMobileInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->getSurveyAnswer();
    }
}
