<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\RenovationDocuments;
use Src\Company\Document\Domain\Repositories\CancellationRepositoryInterface;
use Src\Company\Document\Domain\Repositories\VariationRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Illuminate\Support\Facades\Log;
use Src\Company\Notification\Domain\Services\NotificationService;

// use Src\Company\Project\Domain\Policies\ProjectPolicy;

class StoreCancellationDocumentCommand implements CommandInterface
{
    private CancellationRepositoryInterface $repository;
    private NotificationService $notificationService;

    public function __construct(
        private readonly RenovationDocuments $documents,
        private readonly array $data,
    )
    {
        $this->repository = app()->make(CancellationRepositoryInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
    }

    public function execute(): mixed
    {
        // authorize('storeProperty', ProjectPolicy::class);
        $document = $this->repository->store($this->documents, $this->data);

        // $project = ProjectEloquentModel::with('customersPivot','property')->where('id', $document->project_id)->first();

        // $customerUsers = $project->customersPivot;

        // $projectAddress = $project->property->block_num . " " . $project->property->street_name . (!empty($project->property->unit_num) ? " #" . $project->property->unit_num : '');

        // $title = "Cancellation Created";
        // $message = "New cancellation version has been created for ". $projectAddress;

        // Log::channel('daily')->info($document->type);
        // Log::channel('daily')->info($title);
        // Log::channel('daily')->info($message);

        // foreach ($customerUsers as $customerUser) {

        //     Log::channel('daily')->info($customerUser->device_id);

        //     // Firebase Push Notification
        //     $this->notificationService->sendNotifications(
        //         ['PushNoti'], $title, $message, $customerUser->device_id, 
        //         null, null, null, [], $project->id
        //     );
        // }

        return $document;
    }
}