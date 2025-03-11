<?php

namespace Src\Company\Document\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PurchaseOrderNotiEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $projectName;
    public $salespersonName;
    public $salespersonContact;
    public $vendorName;
    public $requestDate;
    public $managerName;
    public $poNumber;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($projectName,$salespersonName,$salespersonContact,$vendorName,$requestDate,$managerName,$poNumber)
    {
        $this->projectName = $projectName;
        $this->salespersonName = $salespersonName;
        $this->salespersonContact = $salespersonContact;
        $this->vendorName = $vendorName;
        $this->requestDate = $requestDate;
        $this->managerName = $managerName;
        $this->poNumber = $poNumber;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $projectName = $this->projectName;

        $subject = "Purchase order approval request for $projectName";

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
            view: 'emails.purchaseOrderNotiEmail',
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
