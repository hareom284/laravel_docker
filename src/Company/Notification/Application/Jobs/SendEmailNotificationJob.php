<?php

namespace Src\Company\Notification\Application\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Src\Company\Notification\Domain\Services\EmailNotification;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SendEmailNotificationJob implements ShouldQueue
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
                if ($user) {
                    $receiver = $user; // Receiver
                    $now = Carbon::now(); // Timestamp
                    $formattedDate = $now->format('d F y');
                    $user->notify((new EmailNotification($this->title, $this->message, $this->sender, $receiver, $formattedDate))->delay(now()->addSeconds(10)));
                    logger('Sent Email Notification');
                }
            }
        } catch (\Exception $ex) {
            logger(['error', $ex]);
        }
    }
}
