<?php

namespace Src\Company\Notification\Application\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Src\Company\CustomerManagement\Domain\Services\GetUsersToNotifyService;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\ScheduleEloquentModel;
use Src\Company\Notification\Domain\Services\WhatsappNotification;

class SendNotificationEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $timeout = 120;
    public $tries = 1;
    private GetUsersToNotifyService $getUsersService;


    public function __construct()
    {
        $this->getUsersService = app()->make(GetUsersToNotifyService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = Carbon::now();
        $schedules = ScheduleEloquentModel::where('deadline', '<=', $now)->get();
        foreach ($schedules as $schedule) {

            $whatsapp_template = $schedule->whatsapp_template ?? null;
            $whatsapp_language = $schedule->whatsapp_language ?? null;
            $message_types = json_decode($schedule->notification_types);
            if ($message_types && in_array('Reminder', $message_types) && $whatsapp_template && $whatsapp_language) {
                $receiver = $schedule->receiver ?? null;
                $sender = $schedule->sender ?? null;
                if ($whatsapp_template == 'hello_world') {
                    $components = [];
                } else {
                    $sender_name = $sender->first_name . ' ' . $sender->last_name;
                    $receiver_name = $receiver->first_name . ' ' . $receiver->last_name;
                    $current_date = Carbon::now()->format('d/m/Y \a\t H:i \h\o\u\r\s');
                    $components = $this->getWhatsappComponent($receiver_name, $current_date);
                }

                $receiver->notify((new WhatsappNotification($whatsapp_template, $whatsapp_language, $schedule->whatsapp_number, $components))->delay(now()->addSeconds(10)));
            }
            $schedule->delete();
        }
    }

    public function getWhatsappComponent($receiver_name, $current_date)
    {
        $components = [
            [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $receiver_name,
                    ]
                ],
            ],
            [
                'type' => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => $current_date
                    ],
                ],
            ],
        ];
        return $components;
    }
}
