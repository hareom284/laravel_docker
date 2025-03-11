<?php

namespace Src\Company\Notification\Application\Repositories\Eloquent;

use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Src\Company\Project\Infrastructure\EloquentModels\NotificationEloquentModel;
use Src\Company\Notification\Domain\Services\DatabaseNotification as NotificationsDatabaseNotification;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryInterface;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
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

    public function getUserNotifications($filterType, $perPage)
    {
        $user_id = Auth::user()->id;
        $query = DatabaseNotification::where(function ($query) use ($user_id) {
            $query->where('data->sender_id', $user_id)
                ->orWhere('notifiable_id', $user_id);
        })->orderBy('created_at', 'desc');

        if ($filterType == 'sent') {
            $query->where('data->sender_id', $user_id);
        } elseif ($filterType == 'received') {
            $query->where('notifiable_id', $user_id)
                ->where('data->sender_id', '!=', $user_id);
        }

        if (!isset($perPage) && $perPage == "") {
            $notifications = $query->paginate(9999);
        } else {
            $notifications = $query->paginate($perPage);
        }
        return $notifications;
    }

    public function deleteUserNotification($id)
    {
        $notification = DatabaseNotification::find($id);

        if ($notification) {
            $notification->delete();
        }
    }

    public function deleteAllUserNotifications()
    {
        $user = Auth::user();
        $user->notifications()->delete();
    }


    public function getNotificationSetting($message_type)
    {
        $notification_setting = GeneralSettingEloquentModel::where('setting', $message_type)->first();
        return $notification_setting;
    }



    /**
     * Sends browser notifications to multiple users.
     *
     * @param string $title The title of the notification.
     * @param string $message The message of the notification.
     * @param array $user_ids An array of user IDs to send the notification to.
     * @return void
     */
    public function sendBrowserNotification($title, $message, $user_ids)
    {
        foreach ($user_ids as $user_id) {
            $user = UserEloquentModel::find($user_id);
            $sender_id = $user->id == Auth::user()->id ? null : Auth::user()->id;
            $profileUrl = $user->profile_pic ? asset('storage/profile_pic/' . $user->profile_pic) : "";
            $params = [
                'title' => $title,
                'message' => $message,
                'name' => $user->first_name . ' ' . $user->last_name,
                'sender_id' => $sender_id,
                'profile_pic' => $profileUrl
            ];
            DB::beginTransaction();

            try {
                $user->notify(new NotificationsDatabaseNotification($params));
                logger('Sent Database Notification');
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }
}
