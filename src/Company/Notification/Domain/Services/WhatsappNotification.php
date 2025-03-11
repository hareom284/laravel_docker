<?php

namespace Src\Company\Notification\Domain\Services;



use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Src\Company\System\Domain\Channels\WhatsappChannel;

class WhatsappNotification extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    public $timeout = 120;
    public $tries = 1; 

    protected $template_name;
    protected $language_code;
    protected $to;
    protected $components;

    public function __construct($template_name, $language_code, $to, $components=[])
    {
        $this->template_name = $template_name;
        $this->language_code = $language_code;
        $this->to = $to;
        $this->components = $components;
    }

    public function via($notifiable)
    {
        return [WhatsappChannel::class];
    }

    public function toWhatsapp($notifiable)
    {
        return [
            'template_name' => $this->template_name,
            'language_code' => $this->language_code,
            'to' => $this->to,
            'components' => $this->components,
        ];
    }
}
