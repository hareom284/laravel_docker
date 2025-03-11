<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\NotificationRepositoryInterface;

class StoreNotificationCommand implements CommandInterface
{
    private NotificationRepositoryInterface $repository;

    public function __construct(
        private readonly string $message
    )
    {
        $this->repository = app()->make(NotificationRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->message);
    }
}