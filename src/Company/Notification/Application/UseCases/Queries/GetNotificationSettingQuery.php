<?php

namespace Src\Company\Notification\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;

class GetNotificationSettingQuery implements QueryInterface
{
    private NotificationRepositoryInterface $repository;

    public function __construct(
        private readonly string $message_type,
    )
    {
        $this->repository = app()->make(NotificationRepositoryInterface::class);
    }

    public function handle()
    {

        return $this->repository->getNotificationSetting($this->message_type);
    }
}
