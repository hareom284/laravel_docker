<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Services\GetUsersToNotifyService;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\CustomerManagement\Domain\Services\StoreRecordToStatusHistoryService;
use Src\Company\Notification\Domain\Services\NotificationService;
use Src\Company\CustomerManagement\Domain\Services\StoreScheduleRecordService;
use Src\Company\Document\Domain\Repositories\RenovationDocumentMobileInterface;
use Src\Company\Project\Domain\Services\CustomerPaymentCreateService;

// use Src\Company\Project\Domain\Policies\ProjectPolicy;

class StoreRenovationCustomerSignMobileCommand implements CommandInterface
{
    private RenovationDocumentMobileInterface $repository;
    private NotificationService $notificationService;
    private GetUsersToNotifyService $getUsersService;
    private StoreRecordToStatusHistoryService $storeRecordToStatusHistoryService;
    private StoreScheduleRecordService $storeScheduleRecordService;
    private CustomerPaymentCreateService $customerPaymentCreateService;


    public function __construct(
        private readonly array $data,
    ) {
        $this->repository = app()->make(RenovationDocumentMobileInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
        $this->getUsersService = app()->make(GetUsersToNotifyService::class);
        $this->storeRecordToStatusHistoryService = app()->make(StoreRecordToStatusHistoryService::class);
        $this->storeScheduleRecordService = app()->make(StoreScheduleRecordService::class);
        $this->customerPaymentCreateService = app()->make(CustomerPaymentCreateService::class);
    }

    public function execute(): mixed
    {
        // authorize('storeProperty', ProjectPolicy::class);
        $document = $this->repository->customerSignRenoDocument($this->data);

        $project = ProjectEloquentModel::with('customersPivot','property','salespersons')->where('id', $document->project_id)->first();

        $customerUsers = $project->customersPivot;

        $salespersons = $project->salespersons;

        $projectAddress = $project->property->block_num . " " . $project->property->street_name . (!empty($project->property->unit_num) ? " #" . $project->property->unit_num : '');
        
        $title = "";
        $message = "";

        if ($document->type == 'QUOTATION') {

            foreach ($customerUsers as $customerUser) {

                if ($customerUser->customers) {

                    $newCustomer = $customerUser->customers->transition('customerQuotationSign');
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
            $firstCustomer = $customerUsers[0];
            $firstCustomerId = $firstCustomer->customers->id;
            $payment_terms = $document->payment_terms ?? null;
            if($firstCustomerId && $payment_terms){
                $this->customerPaymentCreateService->storePayment($document->project_id,$firstCustomerId,$payment_terms);
            }
        }

        $documentTypes = [
            'QUOTATION' => ['title' => 'Quotation Signed', 'message' => 'New quotation version has been signed for '],
            'CANCELLATION' => ['title' => 'Cancellation Signed', 'message' => 'New cancellation version has been signed for '],
            'VARIATIONORDER' => ['title' => 'Variation Order Signed', 'message' => 'New variation order version has been signed for '],
            'FOC' => ['title' => 'FOC Signed', 'message' => 'New foc version has been signed for ']
        ];
        
        if (isset($documentTypes[$document->type])) {
            $title = $documentTypes[$document->type]['title'];
            $message = $documentTypes[$document->type]['message'] . $projectAddress;
        
            foreach ($salespersons as $salesperson) {
                // Firebase Push Notification
                $this->notificationService->sendNotifications(
                    ['PushNoti'], $title, $message, $salesperson->device_id, 
                    null, null, null, [], $project->id
                );
            }
        }

        return $document;
    }
}
