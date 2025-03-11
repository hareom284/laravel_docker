<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\Project;
// use Src\Company\Project\Domain\Policies\ProjectPolicy;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;
use Src\Company\Notification\Domain\Services\NotificationService;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class UpdateProjectCommand implements CommandInterface
{
    private ProjectRepositoryInterface $repository;
    private NotificationService $notificationService;

    public function __construct(
        private readonly Project $project,
        private readonly array $salesperson_ids,
        private readonly string $agreement_no,
        private readonly array $customer_ids,
        private readonly ?int $id
    ) {
        $this->repository = app()->make(ProjectRepositoryInterface::class);
        $this->notificationService = app()->make(NotificationService::class);
    }

    public function execute(): mixed
    {
        // authorize('update', ProjectPolicy::class);
        $data = $this->repository->update($this->project, $this->salesperson_ids, $this->agreement_no, $this->customer_ids, $this->id);

        $project = ProjectEloquentModel::with('customersPivot','property')->where('id', $this->id)->first();

        $customerUsers = $project->customersPivot;

        $projectAddress = $project->property->block_num . " " . $project->property->street_name . (!empty($project->property->unit_num) ? " #" . $project->property->unit_num : '');   

        foreach ($customerUsers as $customerUser) {
            // Firebase Push Notification
            $this->notificationService->sendNotifications(['PushNoti'], 'Project updated!', $projectAddress." info has been updated.", $customerUser->device_id, null, null, null, [], $project->id);
        }

        return $data;
    }
}
