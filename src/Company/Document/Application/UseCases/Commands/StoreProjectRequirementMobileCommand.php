<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Notification\Domain\Services\NotificationService;

class StoreProjectRequirementMobileCommand implements CommandInterface
{
    private ProjectRequirementMobileRepositoryInterface $repository;
    private NotificationService $notificationService;

    public function __construct(
        private readonly ProjectRequirement $requirement
    )
    {
        $this->repository = app()->make(ProjectRequirementMobileRepositoryInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
    }

    public function execute(): mixed
    {
        // authorize('storeProjectRequirement', DocumentPolicy::class);
        $document = $this->repository->store($this->requirement);

        // $project = ProjectEloquentModel::with('customersPivot','property')->where('id', $document->project_id)->first();

        // $customerUsers = $project->customersPivot;

        // $projectAddress = $project->property->block_num . " " . $project->property->street_name . (!empty($project->property->unit_num) ? " #" . $project->property->unit_num : '');

        // $title = "Project Requirement Created";
        // $message = "New Project Requirement has been created for " . $projectAddress;

        // foreach ($customerUsers as $customerUser) {
        //     // Firebase Push Notification
        //     $this->notificationService->sendNotifications(
        //         ['PushNoti'], $title, $message, $customerUser->device_id, 
        //         null, null, null, [], $project->id
        //     );
        // }

        return $document;
    }
}