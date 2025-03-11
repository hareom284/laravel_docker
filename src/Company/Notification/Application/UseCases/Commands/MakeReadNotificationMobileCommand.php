<?php

namespace Src\Company\Notification\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryMobileInterface;

class MakeReadNotificationMobileCommand implements CommandInterface
{
    private NotificationRepositoryMobileInterface $repository;

    public function __construct(
        private readonly string $id
    )
    {
        $this->repository = app()->make(NotificationRepositoryMobileInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', UserPolicy::class);
        return $this->repository->makeReadNotification($this->id);
    }
}
