<?php

namespace Database\Factories\Observers;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\System\Domain\Services\WhatsappNotification;
use Src\Company\System\Domain\Services\EmailNotification;
use Src\Company\System\Domain\Services\DatabaseNotification as NotificationsDatabaseNotification;
class UserObserver
{
    public function created(UserEloquentModel $user): void
    {

        $receiver = $user; // Receiver
        $now = now(); // Timestamp
        $formattedDate = $now->format('d F y');
        $sender = UserEloquentModel::first();


        $user->notify(new WhatsappNotification('hello_world','eng_us', $user->contact_no));
        $user->notify(new EmailNotification('Welcome Message','Welcome to the System', $sender, $receiver, $formattedDate));
        $user->notify(new NotificationsDatabaseNotification($params));



    }
}
