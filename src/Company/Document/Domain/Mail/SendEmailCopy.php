<?php

namespace Src\Company\Document\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SendEmailCopy extends Mailable
{
    use Queueable, SerializesModels;

    public $companyName;
    public $projectName;
    public $customerName;
    public $salespersonName;
    public $attachment;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($companyName,$projectName,$customerName,$salespersonName,$attachment)
    {
        $this->companyName = $companyName;
        $this->projectName = $projectName;
        $this->customerName = $customerName;
        $this->salespersonName = $salespersonName;
        $this->attachment = $attachment;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $companyName = $this->companyName;

        $projectName = $this->projectName;

        $subject = "$companyName $projectName Document Attached";

        return new Envelope(subject: $subject);

    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.documentSendEmail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        $attachment = $this->attachment;

        if (is_string($attachment) && !str_contains($attachment, "\0") && file_exists($attachment)) {
            
            $mime = mime_content_type($attachment);
    
            return [
                [
                    'file' => $attachment,
                    'options' => [
                        'mime' => $mime,
                    ],
                ],
            ];
        } else {

            return [];
        }
    }
}
