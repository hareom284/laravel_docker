<?php

namespace Src\Company\Notification\Application\Repositories\Eloquent;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;;
use Src\Company\Notification\Domain\Repositories\NotificationRepositoryMobileInterface;
use Src\Company\Notification\Domain\Services\FirebaseNotification;
use stdClass;

class NotificationMobileRepository implements NotificationRepositoryMobileInterface
{
    public function getAppNotifications()
    {
        $user_id = Auth::user()->id;
        $query = DatabaseNotification::where('type', FirebaseNotification::class)->where(function ($query) use ($user_id) {
            $query->where('notifiable_id', $user_id);
        })->orderBy('created_at', 'desc')->get();

        return $query;
    }

    public function makeReadNotification($id)
    {
        $data = DatabaseNotification::find($id);

        if($data)
        {
            $data->markAsRead();
        }

        return $data;

    }

    public function getNotificationStatus()
    {
        $user_id = Auth::user()->id;

        $hasUnreadNotifications = DatabaseNotification::where('type', FirebaseNotification::class)
            ->where('notifiable_id', $user_id)
            ->whereNull('read_at')
            ->exists();

        $data = new stdClass;
        $data->hasUnreadNotifications = $hasUnreadNotifications;

        return $data;
    }
}
