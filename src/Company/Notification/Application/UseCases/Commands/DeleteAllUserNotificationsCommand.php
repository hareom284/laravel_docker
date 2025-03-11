<?php

namespace Src\Company\Notification\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;

class DeleteAllUserNotificationsCommand implements CommandInterface
{
    private NotificationRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(NotificationRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', UserPolicy::class);
        return $this->repository->deleteAllUserNotifications();
    }
}
