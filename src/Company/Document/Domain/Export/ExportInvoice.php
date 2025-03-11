<?php

namespace Src\Company\Document\Domain\Export;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Src\Company\Project\Application\Repositories\Eloquent\CustomerPaymentRepository; // Adjust the namespace if needed
use Src\Company\Project\Domain\Resources\CustomerPaymentResource;
use Illuminate\Support\Facades\Log;

class ExportInvoice implements FromCollection, WithHeadings
{
    private $customerPaymentRepository;

    // Dependency injection of ProjectRepository
    public function __construct(CustomerPaymentRepository $customerPaymentRepository)
    {
        $this->customerPaymentRepository = $customerPaymentRepository;
    }

    public function collection()
    {
        // Fetch all users using the adapted method
        $invoices = $this->customerPaymentRepository->getProjectsForExport();

        // Transform users to a format suitable for export
        $collection = new Collection();
        foreach ($invoices as $invoice) {

            // Create a CustomerPaymentResource instance
            $paymentResource = new CustomerPaymentResource($invoice); // Pass the $invoice object
            // Call the toArray method of CustomerPaymentResource
            $paymentData = $paymentResource->toArray(null); // Pass null as $request since it's not used
            $collection->push([
                'InvoiceNo' => $invoice->project->invoice_no,
                'Customer' => $invoice->project->customer->first_name . ' ' . $invoice->project->customer->last_name,
                'InvoiceDate' => $invoice->created_at->toDateString(),
                'DueDate' => $invoice->created_at->addMonth()->toDateString(),
                'Terms' => '',
                'Location' => '',
                'Memo' => '',
                'Item(Product/Service)' => '',
                'ItemDescription' => $paymentData['type'],
                'ItemQuantity' => 0,
                'ItemRate' => 0,
                'ItemAmount' => $invoice->amount,
                'Taxable' => 'Y',
                'TaxRate' => '9%',
                'Service Date' => $invoice->created_at->addMonth()->toDateString(),
                // Add other fields as needed
            ]);
        }

        return $collection;
    }

    public function headings(): array {
        return['InvoiceNo', 'Customer', 'InvoiceDate', 'DueDate', 'Terms', 'Location', 'Memo', 'Item(Product/Service)', 'ItemDescription', 'ItemQuantity', 'ItemRate', 'ItemAmount', 'Taxable', 'TaxRate', 'Service Date'];
    }
}