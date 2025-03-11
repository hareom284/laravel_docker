<?php

namespace Src\Company\Notification\Application\UseCases\Commands;

use Illuminate\Support\Facades\Auth;
use Src\Common\Domain\CommandInterface;
use Src\Company\Notification\Application\Jobs\SendEmailNotificationJob;
use Src\Company\Notification\Application\Jobs\SendWhatsappNotificationJob;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;
use Src\Company\Notification\Domain\Services\WhatsappNotification;
use Src\Company\Notification\Infrastructure\EloquentModels\UserEloquentModel;

class StoreUserNotificationCommand implements CommandInterface
{
    private NotificationRepositoryInterface $repository;

    public function __construct(
        private readonly ?string $title,
        private readonly ?string $message,
        private readonly ?array $user_ids,
        private readonly ?object $message_type,
    ) {
        $this->repository = app()->make(NotificationRepositoryInterface::class);
    }

    /**
     * Executes the store user notification command.
     *
     * This method sends notifications to users based on their preferred notification settings.
     * It checks the message type settings and sends notifications via WhatsApp, email, and browser
     * to the specified user IDs.
     *
     * @return void
     */
    public function execute()
    {
        if ($this->message_type) {
            $setting_value = json_decode($this->message_type->value);

            if (isset($setting_value->whatsapp) && $setting_value->whatsapp) {
                $template_name = 'hello_world';
                $language = 'eng_us';
                SendWhatsappNotificationJob::dispatch($template_name, $language, $this->user_ids);
            }

            if (isset($setting_value->email) && $setting_value->email) {
                $sender = Auth::user();
                SendEmailNotificationJob::dispatch($this->title, $this->message, $this->user_ids, $sender);
            }

            if (isset($setting_value->browser) && $setting_value->browser) {
                $this->repository->sendBrowserNotification($this->title, $this->message, $this->user_ids);
            }
        }
    }
}
