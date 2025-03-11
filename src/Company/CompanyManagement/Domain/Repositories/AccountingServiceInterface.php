<?php
namespace Src\Company\CompanyManagement\Domain\Repositories;

interface AccountingServiceInterface {

    public function getCustomer(int $companyId, string $name);

    public function getCustomerById(int $companyId, string $contactId);

    public function storeCustomer(int $companyId,array $contactData);

    public function updateCustomer(int $companyId,string $contactId, array $contactData);

    public function getVendorByName(int $companyId, string $name);

    public function getVendorById(int $companyId, $vendorId);

    public function storeVendor(int $companyId, array $contactData);

    public function updateVendor(int $companyId, array $contactData, string $contactId);

    public function getInvoiceByCustomerId(int $companyId, string $customerId);

    public function storeInvoice(int $companyId, array $invoiceData);

    public function updateInvoice(int $companyId, array $invoiceData);

    public function saveInvoicePdf(int $companyId, string $invoiceId);

    public function saveSaleReceiptPdf(int $companyId, string $invoiceId);

    public function storePayment(int $companyId, array $paymentData);

    public function storeBill(int $companyId, array $billData);

    public function storeBillPayment(int $companyId, array $billPaymentData);

    public function storeVendorCredit(int $companyId, array $vendorCreditData);

    public function updateVendorCredit($companyId,$vendorCreditId, $vendorCreditData);

    public function getAllExpenseAccount(int $companyId);

    public function getAllVendors(int $companyId);

    public function getAllAccount(int $companyId);

    public function getAllCustomers(int $companyId);

    public function getProjectByName(int $companyId, string $name);

    public function getBillByCompanyId(int $companyId);

    public function getInvoiceByCompanyId(int $companyId);

    public function getSaleReceiptByCompanyId(int $companyId);

    public function storeClass(int $companyId, string $name);

    public function storeCreditMemo(int $companyId, array $creditMemoData);

    public function saveCreditNotePdf(int $companyId, string $creditNoteId);
}