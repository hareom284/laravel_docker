<?php

namespace Src\Company\Notification\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;

class DeleteUserNotificationCommand implements CommandInterface
{
    private NotificationRepositoryInterface $repository;

    public function __construct(
        private readonly string $id
    )
    {
        $this->repository = app()->make(NotificationRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', UserPolicy::class);
        return $this->repository->deleteUserNotification($this->id);
    }
}
