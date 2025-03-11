<?php

namespace Src\Company\Project\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NotifyExceedSupplierCostingAmountMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $projectAddress;
    protected $salePersons;
    protected $projectRevenue;
    protected $supplierCosting;
    protected $exceedingAmount;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($projectAddress,$salePersons,$projectRevenue,$supplierCosting,$exceedingAmount)
    {
        $this->projectAddress = $projectAddress;
        $this->salePersons = $salePersons;  
        $this->projectRevenue = $projectRevenue;
        $this->supplierCosting = $supplierCosting;
        $this->exceedingAmount = $exceedingAmount;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Notify Exceed Project Revenue In Supplier Costing',
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
            view: 'emails.notifyExceedProjectRevenueInSupplierCosting',
            with: [
                'projectAddress' => $this->projectAddress,
                'salePersons' => $this->salePersons,
                'projectRevenue' => $this->projectRevenue,
                'supplierCosting' => $this->supplierCosting,
                'exceedingAmount' => $this->exceedingAmount,
            ],
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
