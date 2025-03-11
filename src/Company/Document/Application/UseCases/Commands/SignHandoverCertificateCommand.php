<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Services\GetUsersToNotifyService;
use Src\Company\CustomerManagement\Domain\Services\StoreRecordToStatusHistoryService;
use Src\Company\CustomerManagement\Domain\Services\StoreScheduleRecordService;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Document\Domain\Repositories\HandoverCertificateRepositoryInterface;
use Src\Company\Notification\Domain\Services\NotificationService;

class SignHandoverCertificateCommand implements CommandInterface
{
    private HandoverCertificateRepositoryInterface $repository;
    private NotificationService $notificationService;
    private GetUsersToNotifyService $getUsersService;
    private StoreRecordToStatusHistoryService $storeRecordToStatusHistoryService;
    private StoreScheduleRecordService $storeScheduleRecordService;

    public function __construct(
        private $request
    )
    {
        $this->repository = app()->make(HandoverCertificateRepositoryInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
        $this->getUsersService = app()->make(GetUsersToNotifyService::class);
        $this->storeRecordToStatusHistoryService = app()->make(StoreRecordToStatusHistoryService::class);
        $this->storeScheduleRecordService = app()->make(StoreScheduleRecordService::class);
    }

    public function execute(): mixed
    {
        // authorize('storeAreaOfWork', DocumentPolicy::class);
        $result = $this->repository->handoverSign($this->request);
        if ($this->request->customer_signature) {
            $customer_signature_array = $this->request->customer_signature;
            if (count($customer_signature_array) > 0) {
                foreach ($customer_signature_array as $customer_sign) {
                    $customer = CustomerEloquentModel::query()->where('user_id', $customer_sign['customer_id'])->first();
                    if ($customer) {
                        $newCustomer = $customer->transition('completeProject');
                        $setting = $newCustomer ? $newCustomer->currentIdMilestone : null;
                        logger([$setting]);
                        if ($setting) {
                            $this->storeRecordToStatusHistoryService->storeRecord($customer->id, $setting->id);
                            $title = $setting->title;
                            $message = $setting->message;
                            $whatsapp_template = $setting->whatsapp_template ?? null;
                            $whatsapp_language = $setting->whatsapp_language ?? null;
                            $message_types = json_decode($setting->message_type);
                            $roles = json_decode($setting->role) ?? [];
                            $usersToNotify = $this->getUsersService->getUsers($roles, $customer->id);
                            $sender = $newCustomer->user;
                            foreach ($usersToNotify as $user) {
                                $this->storeScheduleRecordService->storeRecord($user, $title, $message, $setting->message_type, $sender->id, $setting->duration, $setting->whatsapp_template_reminder, $setting->whatsapp_language_reminder);
                            }
                            $this->notificationService->sendNotifications($message_types, $title, $message, $usersToNotify, $sender, $whatsapp_template, $whatsapp_language);
                        }
                    }
                }
            }
        }
        return $result;
    }
}