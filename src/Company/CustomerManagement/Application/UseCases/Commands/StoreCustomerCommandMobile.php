<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Carbon\Carbon;
use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\CustomerManagement\Domain\Repositories\CustomerMobileRepositoryInterface;
use Src\Company\CustomerManagement\Domain\Services\GetUsersToNotifyService;
use Src\Company\CustomerManagement\Domain\Services\StoreRecordToStatusHistoryService;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\IdMilestonesEloquentModel;
use Src\Company\Notification\Domain\Services\NotificationService;
use Src\Company\CustomerManagement\Domain\Services\StoreScheduleRecordService;

class StoreCustomerCommandMobile implements CommandInterface
{
    private CustomerMobileRepositoryInterface $repository;
    private NotificationService $notificationService;
    private GetUsersToNotifyService $getUsersService;
    private StoreRecordToStatusHistoryService $storeRecordToStatusHistoryService;
    private StoreScheduleRecordService $storeScheduleRecordService;


    public function __construct(
        private readonly ?Customer $customer = null,
        private readonly ?array $salespersonIds
    ) {
        $this->repository = app()->make(CustomerMobileRepositoryInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
        $this->getUsersService = app()->make(GetUsersToNotifyService::class);
        $this->storeRecordToStatusHistoryService = app()->make(StoreRecordToStatusHistoryService::class);
        $this->storeScheduleRecordService = app()->make(StoreScheduleRecordService::class);
    }

    public function execute(): mixed
    {
        $customer = $this->repository->customerStore($this->customer, $this->salespersonIds);
        $firstIdMilestone = IdMilestonesEloquentModel::orderBy('index', 'asc')->first();
        if ($customer && $firstIdMilestone) {
            $customer->id_milestone_id = $firstIdMilestone->id;
            $customer->save();
            $setting = $customer->currentIdMilestone;
            if ($setting) {
                $this->storeRecordToStatusHistoryService->storeRecord($customer->id, $setting->id);
                $title = $setting->title;
                $message = $setting->message;
                $whatsapp_template = $setting->whatsapp_template ?? null;
                $whatsapp_language = $setting->whatsapp_language ?? null;
                $message_types = json_decode($setting->message_type);
                $roles = json_decode($setting->role) ?? [];
                $usersToNotify = $this->getUsersService->getUsers($roles, $customer->id);
                $sender = $customer->user;
                foreach ($usersToNotify as $user) {
                    $this->storeScheduleRecordService->storeRecord($user, $title, $message, $setting->message_type, $sender->id, $setting->duration, $setting->whatsapp_template_reminder, $setting->whatsapp_language_reminder);
                }
                $this->notificationService->sendNotifications($message_types, $title, $message, $usersToNotify, $sender, $whatsapp_template, $whatsapp_language);
            }
        }
        return $customer;
    }
}
