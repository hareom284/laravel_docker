<?php

namespace Src\Company\System\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $htmlContent;
    public $title;

    public function __construct($htmlContent, $title)
    {
        $this->htmlContent = $htmlContent;
        $this->title = $title;
    }

    public function build()
    {
        return $this->subject($this->title)->html($this->htmlContent);
    }
}
