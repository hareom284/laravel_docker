<?php

namespace Src\Company\Notification\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;

class GetUserNotificationsQuery implements QueryInterface
{
    private NotificationRepositoryInterface $repository;

    public function __construct(
        private readonly ?string $filterType,
        private readonly ?int $perPage
    )
    {
        $this->repository = app()->make(NotificationRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getUserNotifications($this->filterType,$this->perPage);
    }
}
