<?php

namespace Src\Company\Notification\Application\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Src\Company\Notification\Domain\Services\DatabaseNotification;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SendDatabaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 120;
    public $tries = 1; 

    protected $title;
    protected $message;
    protected $user_ids;
    protected $sender;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $message, $user_ids, $sender)
    {
        $this->title = $title;
        $this->message = $message;
        $this->user_ids = $user_ids;
        $this->sender = $sender;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            foreach ($this->user_ids as $user_id) {
                $user = UserEloquentModel::find($user_id);
                $sender_id = $user->id == $this->sender->id ? null : $this->sender->id;
                $profileUrl = $user->profile_pic ? asset('storage/profile_pic/' . $user->profile_pic) : "";
                $params = [
                    'title' => $this->title,
                    'message' => $this->message,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'sender_id' => $sender_id,
                    'profile_pic' => $profileUrl
                ];

                $user->notify((new DatabaseNotification($params))->delay(now()->addSeconds(10)));
            }
        } catch (\Exception $ex) {
            logger(['error', $ex]);
        }
    }
}
