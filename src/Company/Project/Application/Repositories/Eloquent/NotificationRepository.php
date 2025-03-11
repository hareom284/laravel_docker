<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;


use Src\Company\Project\Domain\Repositories\NotificationRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\NotificationEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class NotificationRepository implements NotificationRepositoryInterface
{

    public function store($message)
    {
        $fromUserId = auth('sanctum')->user()->id;

        // Get user IDs of all users with the "manager" role
        $managerIds = UserEloquentModel::whereHas('roles', function ($query) {
            $query->where('name', 'Management');
        })->pluck('id')->toArray();

        foreach ($managerIds as $toUserId) {
            NotificationEloquentModel::create([
                'message' => $message,
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
            ]);
        }

        return true;
    }

}
