<?php

namespace Src\Company\Notification\Domain\Repositories;


interface NotificationRepositoryInterface
{
    public function store($message);
    public function getUserNotifications($filterType,$perPage);
    public function deleteUserNotification($id);
    public function deleteAllUserNotifications();
    public function getNotificationSetting($message_type);
    public function sendBrowserNotification($title, $message, $user_ids);

}
