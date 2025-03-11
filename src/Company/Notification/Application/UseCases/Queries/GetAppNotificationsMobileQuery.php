<?php

namespace Src\Company\Notification\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryMobileInterface;

class GetAppNotificationsMobileQuery implements QueryInterface
{
    private NotificationRepositoryMobileInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(NotificationRepositoryMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAppNotifications();
    }
}
