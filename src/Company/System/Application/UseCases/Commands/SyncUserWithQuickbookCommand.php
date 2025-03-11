<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

class SyncUserWithQuickbookCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;

    public function __construct()
    {
       $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->syncLeadWithQuickbook();
    }
}