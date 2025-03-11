<?php

namespace Src\Company\System\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestCssEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_contents;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_contents)
    {
        $this->email_contents = $email_contents;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'You have been tracked by someone',
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
            view: 'emails.trackemailtestEmail',
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
