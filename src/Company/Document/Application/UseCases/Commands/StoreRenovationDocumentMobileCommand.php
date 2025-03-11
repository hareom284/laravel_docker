<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;
use Src\Company\CustomerManagement\Domain\Services\GetUsersToNotifyService;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\CustomerManagement\Domain\Services\StoreRecordToStatusHistoryService;
use Src\Company\Notification\Domain\Services\NotificationService;
use Src\Company\CustomerManagement\Domain\Services\StoreScheduleRecordService;

class StoreRenovationDocumentMobileCommand implements CommandInterface
{
    private RenovationDocumentMobileInterface $repository;
    private NotificationService $notificationService;
    private GetUsersToNotifyService $getUsersService;
    private StoreRecordToStatusHistoryService $storeRecordToStatusHistoryService;
    private StoreScheduleRecordService $storeScheduleRecordService;

    public function __construct(
        private readonly RenovationDocuments $documents,
        private readonly array $data,
    ) {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
        $this->getUsersService = app()->make(GetUsersToNotifyService::class);
        $this->storeRecordToStatusHistoryService = app()->make(StoreRecordToStatusHistoryService::class);
        $this->storeScheduleRecordService = app()->make(StoreScheduleRecordService::class);
    }

    public function execute(): mixed
    {
        $document = $this->repository->store($this->documents, $this->data);

        $project = ProjectEloquentModel::with('customersPivot','property')->where('id', $document->project_id)->first();

        $customerUsers = $project->customersPivot;

        // $projectAddress = $project->property->block_num . " " . $project->property->street_name . (!empty($project->property->unit_num) ? " #" . $project->property->unit_num : '');
        
        $title = "";
        $message = "";

        if ($document->type == 'QUOTATION') {

            foreach ($customerUsers as $customerUser) {

                if ($customerUser->customers) {

                    if ((int)$document->version_number > 1) {
                        $newCustomer = $customerUser->customers->transition('createNewQuotation');
                    } else {
                        if ($document->salesperson_signature) {
                            $newCustomer = $customerUser->customers->transition('salepersonQuotationSign');
                        } else {
                            $newCustomer = $customerUser->customers->transition('createQuotation');
                        }
                    }

                    $setting = $newCustomer ? $newCustomer->currentIdMilestone : null;
                    if ($setting) {
                        $this->storeRecordToStatusHistoryService->storeRecord($customerUser->customers->id, $setting->id);
                        $title = $setting->title;
                        $message = $setting->message;
                        $whatsapp_template = $setting->whatsapp_template ?? null;
                        $whatsapp_language = $setting->whatsapp_language ?? null;
                        $message_types = json_decode($setting->message_type);
                        $roles = json_decode($setting->role) ?? [];
                        $usersToNotify = $this->getUsersService->getUsers($roles, $customerUser->customers->id);
                        $sender = $newCustomer->user;
                        foreach ($usersToNotify as $user) {
                            $this->storeScheduleRecordService->storeRecord($user, $title, $message, $setting->message_type, $sender->id, $setting->duration, $setting->whatsapp_template_reminder, $setting->whatsapp_language_reminder);
                        }
                        $this->notificationService->sendNotifications($message_types, $title, $message, $usersToNotify, $sender, $whatsapp_template, $whatsapp_language);
                    }
                }
            }
        }

        // $documentTypes = [
        //     'QUOTATION' => ['title' => 'Quotation Created', 'message' => 'New quotation version has been created for '],
        //     'CANCELLATION' => ['title' => 'Cancellation Created', 'message' => 'New cancellation version has been created for '],
        //     'VARIATIONORDER' => ['title' => 'Variation Order Created', 'message' => 'New variation order version has been created for '],
        //     'FOC' => ['title' => 'FOC Created', 'message' => 'New foc version has been created for ']
        // ];
        
        // if (isset($documentTypes[$document->type])) {
        //     $title = $documentTypes[$document->type]['title'];
        //     $message = $documentTypes[$document->type]['message'] . $projectAddress;

        //     foreach ($customerUsers as $customerUser) {
        //         // Firebase Push Notification
        //         $this->notificationService->sendNotifications(
        //             ['PushNoti'], $title, $message, $customerUser->device_id, 
        //             null, null, null, [], $project->id
        //         );
        //     }
        // }

        return $document;
    }
}
