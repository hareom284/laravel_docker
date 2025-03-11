<?php

namespace Src\Company\Notification\Domain\Repositories;


interface NotificationRepositoryMobileInterface
{
    public function getAppNotifications();

    public function makeReadNotification($id);

    public function getNotificationStatus();

}
