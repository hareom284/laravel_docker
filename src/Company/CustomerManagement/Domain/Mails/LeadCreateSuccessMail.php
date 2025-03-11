<?php

namespace Src\Company\CustomerManagement\Domain\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadCreateSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $siteSetting;
    public $salespersonNames;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$email,$password,$siteSetting, $salespersonNames)
    {
        $this->name = $name;

        $this->email = $email;

        $this->password = $password;

        $this->siteSetting = $siteSetting;

        $this->salespersonNames =$salespersonNames;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Welcome - Your Login Information',
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
            view: 'emails.leadCreateEmail',
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
