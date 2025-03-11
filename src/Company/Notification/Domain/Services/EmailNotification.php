<?php

namespace Src\Company\Notification\Domain\Services;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class EmailNotification extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 120;
    public $tries = 1; 
    protected $title;
    protected $content;
    protected $sender;
    protected $receiver;
    protected $created_at;

    public function __construct($title, $content, $sender, $receiver, $created_at)
    {
        $this->title = $title;
        $this->content = $content;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->created_at = $created_at;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.testEmail', [
                'title' => $this->title,
                'content' => $this->content,
                'sender' => $this->sender,
                'receiver' => $this->receiver,
                'created_at' => $this->created_at
            ]);
    }
}
