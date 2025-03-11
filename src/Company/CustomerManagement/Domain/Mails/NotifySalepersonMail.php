<?php

namespace Src\Company\CustomerManagement\Domain\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifySalepersonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $salepersonName;
    public $customerName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($salepersonName,$customerName)
    {
        $this->salepersonName = $salepersonName;

        $this->customerName = $customerName;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'You have been assigned to work with a customer',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.notifySalepersonEmail',
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

     /**
     * Build the message.
     *
     * @return $this
     */
    // public function build()
    // {
    //     return $this->view('systemerror.sytemerror')
    //                 ->with('content', $this->content);
    // }
}
