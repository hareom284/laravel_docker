<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Illuminate\Support\Facades\Auth;
use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Services\GetUsersToNotifyService;
use Src\Company\CustomerManagement\Domain\Services\StoreRecordToStatusHistoryService;
use Src\Company\CustomerManagement\Domain\Services\StoreScheduleRecordService;
use Src\Company\Notification\Domain\Services\NotificationService;

class UpdateCustomerCommand implements CommandInterface
{
    private CustomerRepositoryInterface $repository;
    private NotificationService $notificationService;
    private GetUsersToNotifyService $getUsersService;
    private StoreRecordToStatusHistoryService $storeRecordToStatusHistoryService;
    private StoreScheduleRecordService $storeScheduleRecordService;

    public function __construct(
        private readonly mixed $user
    )
    {
        $this->repository = app()->make(CustomerRepositoryInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
        $this->getUsersService = app()->make(GetUsersToNotifyService::class);
        $this->storeRecordToStatusHistoryService = app()->make(StoreRecordToStatusHistoryService::class);
        $this->storeScheduleRecordService = app()->make(StoreScheduleRecordService::class);

    }

    public function execute(): mixed
    {
        // authorize('update', UserPolicy::class);
        $customer= $this->repository->customerUpdate($this->user);
        if ($customer) {
            if($customer->currentIdMilestone?->name == 'Contacted' && $customer->next_meeting){
                $newCustomer = $customer->transition('enterAppointmentDate');
            } else {
                $newCustomer = $customer->transition('updateLead');
            }
            $setting = $newCustomer ? $newCustomer->currentIdMilestone: null;
            if($setting){
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
        return $customer;
    }
}
