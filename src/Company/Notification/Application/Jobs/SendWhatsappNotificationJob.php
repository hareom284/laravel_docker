<?php

namespace Src\Company\Notification\Application\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Src\Company\Notification\Domain\Services\WhatsappNotification;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SendWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 120;
    public $tries = 1;

    protected $template_name;
    protected $language;
    protected $user_ids;
    protected $components;
    protected $sender;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template_name, $language, $user_ids, $components, $sender)
    {
        $this->template_name = $template_name;
        $this->language = $language;
        $this->user_ids = $user_ids;
        $this->components = $components;
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
                    if ($this->template_name == 'hello_world') {
                        $this->components = [];
                    } else {
                        $current_date = Carbon::now()->format('d/m/Y \a\t H:i \h\o\u\r\s');
                        $sender_name = $this->sender->first_name . ' ' . $this->sender->last_name;
                        $receiver_name = $user->first_name . ' ' . $user->last_name;
                        $this->components = $this->getWhatsappComponent($receiver_name, $current_date);
                    }
                    $contact_no = $user->prefix ? $user->prefix . $user->contact_no : $user->contact_no;
                    $user->notify((new WhatsappNotification($this->template_name, $this->language, $contact_no, $this->components))->delay(now()->addSeconds(10)));
                    logger('Sent Whatsapp Notification');
                }
            }
        } catch (\Exception $ex) {
            logger(['error', $ex]);
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
