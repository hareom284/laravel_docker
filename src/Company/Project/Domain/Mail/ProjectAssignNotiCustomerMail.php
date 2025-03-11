<?php

namespace Src\Company\Project\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProjectAssignNotiCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerEmail;
    public $customerName;
    public $customerPassword;
    public $companyName;
    public $salespersonName;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($customerEmail,$customerName,$customerPassword,$companyName,$salespersonName)
    {
        $this->customerEmail = $customerEmail;

        $this->customerName = $customerName;

        $this->customerPassword = $customerPassword;

        $this->companyName = $companyName;

        $this->salespersonName = $salespersonName;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $companyName = $this->companyName;

        $subject = "Welcome to $companyName - Your Login Information";

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
            view: 'emails.projectCreateEmail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
