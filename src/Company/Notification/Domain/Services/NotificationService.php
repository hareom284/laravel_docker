<?php

namespace Src\Company\Notification\Domain\Services;

use Src\Company\Notification\Application\Jobs\SendDatabaseNotificationJob;
use Src\Company\Notification\Application\Jobs\SendEmailNotificationJob;
use Src\Company\Notification\Application\Jobs\SendPushNotificationJob;
use Src\Company\Notification\Application\Jobs\SendWhatsappNotificationJob;

class NotificationService
{

    public function sendNotifications(?array $message_types, ?string $title, ?string $message, mixed $usersToNotify, $sender = null, string $whatsapp_template = null, string $whatsapp_language = null, $whatsapp_components = [], $projectId = null): void
    {
        if ($usersToNotify) {
            if($message_types && $message_types!='null'){
                foreach ($message_types as $message_type) {
                    switch ($message_type) {
                        case 'Notification':
                            logger('send notification');
                            SendDatabaseNotificationJob::dispatch($title, $message, $usersToNotify, $sender);
                            break;
                        case 'Actions':
                            SendWhatsappNotificationJob::dispatch($whatsapp_template, $whatsapp_language, $usersToNotify, $whatsapp_components, $sender);
                            logger('send Actions');
                            break;
                        case 'Email':
                            SendEmailNotificationJob::dispatch($title, $message, $usersToNotify, $sender);
                            logger('send email');
                            break;
                        case 'PushNoti':
                            SendPushNotificationJob::dispatch($title, $message, $usersToNotify, $projectId);
                            logger('send push noti');
                            break;
                    }
                }
            }
        }
    }


}
