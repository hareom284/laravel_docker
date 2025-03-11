<?php

namespace Src\Company\System\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $htmlContent;

    public function __construct($htmlContent)
    {
        $this->htmlContent = $htmlContent;
    }

    public function build()
    {
        return $this->html($this->htmlContent);
    }
}
