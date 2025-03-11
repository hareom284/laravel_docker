<?php

namespace Src\Company\CompanyManagement\Domain\Services;

use DateTime;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use XeroAPI\XeroPHP\ApiException;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Api\AccountingApi;
use Illuminate\Support\Facades\Storage;
use XeroAPI\XeroPHP\Models\Accounting\Phone;
use XeroAPI\XeroPHP\Models\Accounting\Address;
use XeroAPI\XeroPHP\Models\Accounting\Contact;
use XeroAPI\XeroPHP\Models\Accounting\Invoice;
use XeroAPI\XeroPHP\Models\Accounting\Payment;
use XeroAPI\XeroPHP\Models\Accounting\Accounts;
use XeroAPI\XeroPHP\Models\Accounting\Contacts;
use XeroAPI\XeroPHP\Models\Accounting\Invoices;
use XeroAPI\XeroPHP\Models\Accounting\LineItem;
use XeroAPI\XeroPHP\Models\Accounting\CreditNote;
use XeroAPI\XeroPHP\Models\Accounting\CreditNotes;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QuickBookEloquentModel;
use XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes;

class XeroService implements AccountingServiceInterface
{
    protected $accountingApi;
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;
    protected $refreshToken;
    protected $tenantId;

    private function loadSettings(): void
    {
        $settings = GeneralSettingEloquentModel::whereIn('setting', [  
                'xero_client_id',
                'xero_client_secret',
        ])->pluck('value', 'setting');

        $this->clientId = $settings['xero_client_id'];
        $this->clientSecret = $settings['xero_client_secret'];

    }

    private function getCredential($companyID): QuickBookEloquentModel
    {
        return QuickBookEloquentModel::where('company_id', $companyID)->first();
    }

    public function getAccessToken($companyID): void
    {
        try {
            $this->loadSettings();

            $credential = $this->getCredential($companyID);

            $originalRefreshToken = $credential->refresh_token;
            $this->tenantId = $credential->accounting_software_company_id;

            $tokenUrl = 'https://identity.xero.com/connect/token';

            $authToken = base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = Http::withHeaders([
                'authorization' => 'Basic ' . $authToken,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'grant_type' => 'refresh_token',
            ])->asForm()->post($tokenUrl, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $originalRefreshToken,
                'client_id' => $this->clientId,
            ]);

            if ($response->successful()) {

                $data = $response->json();
               
                $this->accessToken = $data['access_token'];
                $this->refreshToken = $data['refresh_token'];

                Log::info('Xero Access Token: ' . $this->accessToken);
                Log::info('Xero Refresh Token: ' . $this->refreshToken);
            } else {

                throw new Exception('HTTP Request failed: ' . $response->body());
            }

            // Update refresh token  it has changed
            if ($originalRefreshToken !== $this->refreshToken) {
                $credential->refresh_token = $this->refreshToken;
                $credential->save();
            }

            $config = Configuration::getDefaultConfiguration()->setAccessToken($this->accessToken);

            // Set up Accounting API
            $this->accountingApi = new AccountingApi(
                new \GuzzleHttp\Client(),
                $config
            );
        } catch (\Exception $e) {
            throw new \Exception('Error obtaining Xero access token.'. $e->getMessage());
        }
    }

    // Redirect to Xero for initial authentication
    /*
    public function redirectTo()
    {
        $provider = new GenericProvider([
            'clientId'                => $this->clientId,   
            'clientSecret'            => $this->clientSecret,
            'redirectUri'             => $this->redirectUri,
            'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken'          => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
        ]);

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => 'openid email profile accounting.transactions accounting.contacts accounting.settings accounting.attachments offline_access'
        ]);

        session(['oauth2state' => $provider->getState()]);
        return $authUrl;
    }

    // Handle Xero callback after authentication
    public function handleCallback(array $callbackData)
    {
        try {
            $provider = new GenericProvider([
                'clientId'                => $this->clientId,   
                'clientSecret'            => $this->clientSecret,
                'redirectUri'             => $this->redirectUri,
                'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
                'urlAccessToken'          => 'https://identity.xero.com/connect/token',
                'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
            ]);

            $token = $provider->getAccessToken('authorization_code', [
                'code' => $callbackData['code']
            ]);
            $this->accessToken = $token->getToken();
            $connections = $this->getTenantConnections();
            $this->tenantId = $connections[0]->getTenantId();

            $settings = [
                'xero_client_id' => $this->clientId,
                'xero_client_secret' => $this->clientSecret,
                'xero_redirect_uri' => $this->redirectUri,
                'xero_access_token' => $token->getToken(),
                'xero_refresh_token' => $token->getRefreshToken(),
                'xero_expired_at' => Carbon::now()->addSeconds($token->getExpires()),
                'xero_tenant_id' => $this->tenantId
            ];
    
            foreach ($settings as $key => $value) {
                GeneralSettingEloquentModel::updateOrCreate(
                    ['setting' => $key],
                    ['value' => $value, 'is_array' => 0]
                );
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }*/

    public function getCustomer($companyId, $customerName)
    {
        try {
            $this->getAccessToken($companyId);
           
            $filter = "Name == \"$customerName\" AND IsCustomer == true";

            $contacts = $this->accountingApi->getContacts($this->tenantId, null, $filter)->getContacts();
            
            $xeroUserId = $contacts[0]->getContactId();

            return $xeroUserId;
    
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve customer contacts: " . $e->getMessage());
        }
    }

    public function getCustomerById($companyId, $contactId)
    {
        $this->getAccessToken($companyId);
        try {
            $contact = $this->accountingApi->getContactById($this->tenantId, $contactId)->getContact();


            $xeroUserId = $contact[0]->getContactId();

            return $xeroUserId;
    
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve customer contacts: " . $e->getMessage());
        }
    }

    public function storeCustomer($companyId, $contactData)
    {
        try {
            $this->getAccessToken($companyId);
            if (empty($contactData['name'])) {
                throw new \Exception('Customer name are required.');
            }

            $contact = new Contact();
            $contact->setName($contactData['name'])
                ->setEmailAddress($contactData['email'])
                ->setIsCustomer(true);

            if (!empty($contactData['first_name'])) {
                $contact->setFirstName($contactData['first_name']);
            }
            if (!empty($contactData['last_name'])) {
                $contact->setLastName($contactData['last_name']);
            }

            if (!empty($contactData['contact_no'])) {
                $contact->setPhones([
                    [
                        'phone_type' => 'DEFAULT',
                        'phone_number' => $contactData['contact_no'],
                    ]
                ]);
            }

            if (!empty($contactData['address']) && !empty($contactData['postal_code'])) {
                $contact->setAddresses([
                    [
                        'address_type' => 'STREET',
                        'address_line1' => $contactData['address'],
                        'city' => "Singapore",
                        'postal_code' => $contactData['postal_code']
                    ]
                ]);
            }
            $arr_contacts = [];
            array_push($arr_contacts, $contact);
            $contacts = new Contacts;
            $contacts->setContacts($arr_contacts);
            $createdContact = $this->accountingApi->createContacts($this->tenantId, $contacts);

            $xeroUserId = $createdContact[0]->getContactId();

            Log::info('Xero User ID: ' . $xeroUserId);

            return $xeroUserId;

        } catch (ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create customer: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create customer: " . $e->getMessage());
        }
    }

    public function updateCustomer($companyId, $contactId, $contactData)
    {
        try {

            $this->getAccessToken($companyId);

            $contact = new Contact;
            $contact->setContactId($contactId)
                ->setName($contactData['name'] ?? $contact->getName())
                ->setEmailAddress($contactData['email'] ?? $contact->getEmailAddress());

            if (!empty($contactData['first_name'])) {
                $contact->setFirstName($contactData['first_name']);
            }
            if (!empty($contactData['last_name'])) {
                $contact->setLastName($contactData['last_name']);
            }

            if (!empty($contactData['contact_no'])) {
                $phone = new Phone();
                $phone->setPhoneType(Phone::PHONE_TYPE__DEFAULT)
                      ->setPhoneNumber($contactData['contact_no']);
                $contact->setPhones([$phone]);
            }

            if (!empty($contactData['address']) && !empty($contactData['city']) && !empty($contactData['postal_code'])) {
                $address = new Address();
                $address->setAddressType(Address::ADDRESS_TYPE_STREET)
                        ->setAddressLine1($contactData['address'])
                        ->setCity($contactData['city'])
                        ->setPostalCode($contactData['postal_code']);
                $contact->setAddresses([$address]);
            }
            // dd($contact);

            $contacts = new Contacts();
            $arr_contacts = [];
            array_push($arr_contacts, $contact);
            $contacts->setContacts($arr_contacts);

            $updatedContact = $this->accountingApi->updateContact($this->tenantId, $contactId, $contacts);
            return $updatedContact->getContacts()[0];

        } catch (ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to update customer: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to update customer: " . $e->getMessage());
        }
    }

    public function getAllVendors($companyId)
    {
        $this->getAccessToken($companyId);
        try {
            $filter = 'IsSupplier == true';
            $vendors = $this->accountingApi->getContacts($this->tenantId, null, $filter)->getContacts();
            return $vendors;
    
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve customer contacts: " . $e->getMessage());
        }
    }

    public function getVendorByName($companyId, $vendorName)
    {
        try {

            $this->getAccessToken($companyId);
           
            $filter = " Name == \"$vendorName\" ";

            $contacts = $this->accountingApi->getContacts($this->tenantId, null, $filter)->getContacts(); 

            if(count($contacts) > 0){

                $xeroVendorId = $contacts[0]->getContactId();

                return $xeroVendorId;

            }else{

                Log::info('Vendor not found in Xero');
                
                return null;
            }
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve customer contacts: " . $e->getMessage());
        }
    }

    //Finished
    public function storeVendor($companyId, $contactData)
    {
        try {
            
            $this->getAccessToken($companyId);

            if (empty($contactData['name'])) {
                throw new \Exception('Vendor name are required.');
            }

            $contact = new Contact();

            $contact->setName($contactData['name'])
                ->setEmailAddress($contactData['email'])
                ->setIsSupplier(true)
                ->setIsCustomer(false);

            if (!empty($contactData['contact_person'])) {
                $contact->setFirstName($contactData['contact_person']);
            }
            if (!empty($contactData['contact_person_last_name'])) {
                $contact->setLastName($contactData['contact_person_last_name']);
            }

            if (!empty($contactData['contact_no'])) {
                $contact->setPhones([
                    [
                        'phone_type' => 'DEFAULT',
                        'phone_number' => $contactData['contact_no'],
                    ]
                ]);
            }

            if (!empty($contactData['address']) && !empty($contactData['city']) && !empty($contactData['postal_code'])) {
                $contact->setAddresses([
                    [
                        'address_type' => 'STREET',
                        'address_line1' => $contactData['address'],
                        'city' => $contactData['city'],
                        'postal_code' => $contactData['postal_code']
                    ]
                ]);
            }

            $arr_contacts = [];

            array_push($arr_contacts, $contact);

            $contacts = new Contacts;
            $contacts->setContacts($arr_contacts);
            $createdContact = $this->accountingApi->createContacts($this->tenantId, $contacts);

            $xeroVendorId = $createdContact[0]->getContactId();

            Log::info('Xero Vendor ID: ' . $xeroVendorId);

            return $xeroVendorId;

        } catch (ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create vendor: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create vendor: " . $e->getMessage());
        }
    }

    public function updateVendor($companyId, $contactId, $contactData)
    {
        try {

            $this->getAccessToken($companyId);

            $contact = new Contact;
            $contact->setContactId($contactId)
                ->setName($contactData['name'] ?? $contact->getName())
                ->setEmailAddress($contactData['email'] ?? $contact->getEmailAddress());

            if (!empty($contactData['first_name'])) {
                $contact->setFirstName($contactData['first_name']);
            }
            if (!empty($contactData['last_name'])) {
                $contact->setLastName($contactData['last_name']);
            }

            if (!empty($contactData['contact_no'])) {
                $phone = new Phone();
                $phone->setPhoneType(Phone::PHONE_TYPE__DEFAULT)
                      ->setPhoneNumber($contactData['contact_no']);
                $contact->setPhones([$phone]);
            }

            if (!empty($contactData['address']) && !empty($contactData['city']) && !empty($contactData['postal_code'])) {
                $address = new Address();
                $address->setAddressType(Address::ADDRESS_TYPE_STREET)
                        ->setAddressLine1($contactData['address'])
                        ->setCity($contactData['city'])
                        ->setPostalCode($contactData['postal_code']);
                $contact->setAddresses([$address]);
            }

            $contacts = new Contacts();
            $arr_contacts = [];
            array_push($arr_contacts, $contact);
            $contacts->setContacts($arr_contacts);

            $updatedContact = $this->accountingApi->updateContact($this->tenantId, $contactId, $contacts);
            return $updatedContact->getContacts()[0];

        } catch (ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to update vendor: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to update vendor: " . $e->getMessage());
        }
    }

    public function getInvoiceByCustomerId($companyID, $customerId)
    {
        try {
            $this->getAccessToken($companyID);

            $filter = "Contact.ContactID == Guid(\"$customerId\")";
            $invoices = $this->accountingApi->getInvoices($this->tenantId, null, $filter);

            if (!$invoices->getInvoices()) {
                throw new \Exception("No invoices found for customer ID: " . $customerId);
            }

            return $invoices->getInvoices();

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Error fetching invoices: " . $errorBody);
        }
    }

    public function storeInvoice($companyId, $invoiceData)
    {
        try {
            $this->getAccessToken($companyId);

            $contact = $this->accountingApi->getContact($this->tenantId, $invoiceData['customerId'])->getContacts();

            $lineItem = new LineItem();
            $lineItem->setDescription($invoiceData['name'])
                ->setQuantity(1)
                ->setLineAmount($invoiceData['taxInclusiveAmt'])
                ->setAccountCode($invoiceData['account_code'] ?? '200')
                ->setTaxType('OUTPUT');

            $lineItems = [];
            array_push($lineItems, $lineItem);

            $invoice = new Invoice();
            $invoice->setDate(Carbon::now());
            $invoice->setDueDate(new \DateTime($invoiceData['due_date'] ?? '+5 days'));
            $invoice->setType(Invoice::TYPE_ACCREC);
            $invoice->setContact($contact[0]);
            $invoice->setReference($invoiceData['name']);
            $invoice->setLineAmountTypes('Inclusive');
            $invoice->setStatus(Invoice::STATUS_AUTHORISED);
            $invoice->setCurrencyCode('SGD');
            $invoice->setCurrencyRate(1);
            $invoice->setLineItems($lineItems);
            $invoice->setTotalTax($invoiceData['totalTax']);
            $invoice->setSubTotal($invoiceData['taxInclusiveAmt']);
            $invoice->setTotal($invoiceData['taxInclusiveAmt']);

            if (!empty($invoiceData['remark'])) {
                $invoice->setReference($invoiceData['remark']);
            }

            $invoices = new Invoices;
            $arr_invoices = [];
            array_push($arr_invoices, $invoice);
            $invoices->setInvoices($arr_invoices);

            $createdInvoice = $this->accountingApi->createInvoices($this->tenantId, $invoices);
            $xeroInvoiceId = $createdInvoice[0];

            return $xeroInvoiceId;

        } catch (ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create invoice: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create invoice: " . $e->getMessage());
        }
    }

    public function updateInvoice($companyId, $invoiceData)
    {
        try {
            $this->getAccessToken($companyId);

            $existingInvoice = $this->accountingApi->getInvoice($this->tenantId, $invoiceData['invoiceId'])->getInvoices()[0];

            $contact = $this->accountingApi->getContact($this->tenantId, $invoiceData['customerId'])->getContacts()[0];

            $lineItem = new LineItem();
            $lineItem->setDescription($invoiceData['name'])
                ->setQuantity(1)
                ->setUnitAmount($invoiceData['netAmountTaxable'])
                ->setAccountCode($invoiceData['account_code'] ?? '200')
                ->setTaxType('OUTPUT');

            $existingInvoice->setContact($contact);
            $existingInvoice->setDate(Carbon::now());
            $existingInvoice->setDueDate(new \DateTime($invoiceData['due_date'] ?? '+30 days'));
            $existingInvoice->setType(Invoice::TYPE_ACCREC);
            $existingInvoice->setReference($invoiceData['name']);
            $existingInvoice->setLineAmountTypes($invoiceData['globalTaxCalculation']);
            $existingInvoice->setStatus(Invoice::STATUS_AUTHORISED);
            $existingInvoice->setCurrencyCode('SGD');
            $existingInvoice->setCurrencyRate(1);
            $existingInvoice->setLineItems([$lineItem]);
            $existingInvoice->setTotalTax($invoiceData['totalTax']);
            $existingInvoice->setSubTotal($invoiceData['netAmountTaxable']);
            $existingInvoice->setTotal($invoiceData['taxInclusiveAmt']);

            if (!empty($invoiceData['remark'])) {
                $existingInvoice->setReference($invoiceData['remark']);
            }

            $invoices = new Invoices();
            $arr_invoices = [];
            array_push($arr_invoices, $existingInvoice);
            $invoices->setInvoices($arr_invoices);

            $resultingInvoice = $this->accountingApi->updateInvoice($this->tenantId, $invoiceData['invoiceId'], $invoices);
            return $resultingInvoice->getInvoices()[0];

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to update invoice: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to update invoice: " . $e->getMessage());
        }
    }

    public function saveInvoicePdf($companyId, $invoiceId)
    {
        try {
            $this->getAccessToken($companyId);

            // Fetch the invoice PDF from Xero as SplFileObject
            $invoicePdfFile = $this->accountingApi->getInvoiceAsPdf($this->tenantId, $invoiceId);

            // Ensure the storage directory exists
            $directory = 'XeroInvoices';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Define the file path
            $fileName = 'invoice_' . time() . '.pdf';

            // Save the SplFileObject content to the specified file path
            $fileStream = $invoicePdfFile->fread($invoicePdfFile->getSize());
            Storage::disk('public')->put($directory . '/' . $fileName, $fileStream);

            // Return the relative file path
            return $directory . '/' . $fileName;

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to save invoice PDF: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to save invoice PDF: " . $e->getMessage());
        }
    }

    /*
    public function storeAccount($accountData)
    {
        try {
            $account = new Account();
            $account->setName($accountData['name'])
                    ->setCode($accountData['code'])
                    ->setType($accountData['type']) // The account type (e.g., 'EXPENSE', 'REVENUE', 'BANK')
                    ->setTaxType($accountData['tax_type'] ?? 'NONE'); // Tax type (e.g., 'NONE', 'OUTPUT', 'INPUT')

            if (!empty($accountData['description'])) {
                $account->setDescription($accountData['description']);
            }

            if (!empty($accountData['bank_account_number']) && $accountData['type'] == 'BANK') {
                $account->setBankAccountNumber($accountData['bank_account_number']);
            }

            $accounts = new Accounts();
            $accounts->setAccounts([$account]);

            $createdAccount = $this->accountingApi->createAccount($this->tenantId, $accounts);
            return $createdAccount->getAccounts()[0];

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create account: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create account: " . $e->getMessage());
        }
    }
    */

    public function storePayment($companyId, $paymentData)
    {
        try {
            $this->getAccessToken($companyId);

            $invoice = $this->accountingApi->getInvoice($this->tenantId, $paymentData['invoiceId'])->getInvoices()[0];
            $account = $this->accountingApi->getAccount($this->tenantId, $paymentData['accountId'])->getAccounts()[0];

            $payment = new Payment();
            $payment->setInvoice($invoice);
            $payment->setAmount($paymentData['amount']);
            $payment->setDate(new \DateTime());
            $payment->setAccount($account);
            $payment->setCurrencyRate(1);
            $payment->setStatus(Payment::STATUS_AUTHORISED);
            $createdPayment = $this->accountingApi->createPayment($this->tenantId, $payment);

            $xeroPayment= $createdPayment[0];

            return $xeroPayment;

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create payment: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create payment: " . $e->getMessage());
        }
    }

    public function storeBill($companyId, $billData)
    {
        try {
            $this->getAccessToken($companyId);

            $contact = $this->accountingApi->getContact($this->tenantId, $billData['vendorID'])->getContacts();

            $billDate = new DateTime($billData['invoiceDate']);
            $dueDate = (clone $billDate)->modify('+3 days');

            $lineItem = new LineItem();
            $lineItem->setDescription($billData['description'])
                ->setQuantity(1)
                ->setUnitAmount($billData['amount'])
                ->setAccountCode($billData['account_code'] ?? '200')
                ->setTaxType($billData['taxCodeRef']);

            $invoice = new Invoice();
            $invoice->setDate($billDate);
            $invoice->setDueDate($dueDate);
            $invoice->setType(Invoice::TYPE_ACCPAY);
            $invoice->setContact($contact[0]);
            $invoice->setInvoiceNumber($billData['invoiceNo']);
            $invoice->setReference($billData['description']);
            $invoice->setLineAmountTypes($billData['globalTaxCalculation']);
            $invoice->setStatus(Invoice::STATUS_AUTHORISED);
            $invoice->setCurrencyCode('SGD');
            $invoice->setCurrencyRate(1);
            $invoice->setLineItems([$lineItem]);
            $invoice->setTotalTax($billData['totalTax']);
            $invoice->setSubTotal($billData['amount']);
            $invoice->setTotal($billData['totalAmount']);

            if (!empty($billData['PrivateNote'])) {
                $invoice->setReference($billData['PrivateNote']);
            }

            $invoices = new Invoices;
            $arr_invoices = [];
            array_push($arr_invoices, $invoice);
            $invoices->setInvoices($arr_invoices);

            $createdInvoice = $this->accountingApi->createInvoices($this->tenantId, $invoices);
            $xeroInvoiceId = $createdInvoice[0];

            return $xeroInvoiceId;
            
        } catch (ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create invoice: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create invoice: " . $e->getMessage());
        }
    }

    public function updateBill($companyId, $invoiceData)
    {
        try {
            $this->getAccessToken($companyId);

            $existingInvoice = $this->accountingApi->getInvoice($this->tenantId, $invoiceData['invoiceId'])->getInvoices()[0];

            $contact = $this->accountingApi->getContact($this->tenantId, $invoiceData['vendorId'])->getContacts()[0];

            $lineItem = new LineItem();
            $lineItem->setDescription($invoiceData['name'])
                ->setQuantity(1)
                ->setUnitAmount($invoiceData['netAmountTaxable'])
                ->setAccountCode('200')
                ->setTaxType('NONE');

            $existingInvoice->setContact($contact);
            $existingInvoice->setDate(Carbon::now());
            $existingInvoice->setDueDate(new \DateTime($invoiceData['due_date'] ?? '+30 days'));
            $existingInvoice->setType(Invoice::TYPE_ACCPAY);
            $existingInvoice->setReference($invoiceData['name']);
            $existingInvoice->setLineAmountTypes('Exclusive');
            $existingInvoice->setStatus(Invoice::STATUS_AUTHORISED);
            $existingInvoice->setCurrencyCode('SGD');
            $existingInvoice->setCurrencyRate(1);
            $existingInvoice->setLineItems([$lineItem]);
            $existingInvoice->setTotalTax($invoiceData['totalTax']);
            $existingInvoice->setSubTotal($invoiceData['netAmountTaxable']);
            $existingInvoice->setTotal($invoiceData['taxInclusiveAmt']);

            if (!empty($invoiceData['remark'])) {
                $existingInvoice->setReference($invoiceData['remark']);
            }

            $invoices = new Invoices();
            $arr_invoices = [];
            array_push($arr_invoices, $existingInvoice);
            $invoices->setInvoices($arr_invoices);

            $resultingInvoice = $this->accountingApi->updateInvoice($this->tenantId, $invoiceData['invoiceId'], $invoices);
            return $resultingInvoice->getInvoices()[0];

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to update invoice: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to update invoice: " . $e->getMessage());
        }
    }

    public function storeBillPayment($companyId, $paymentData)
    {
        try {
            $this->getAccessToken($companyId);

            $invoice = $this->accountingApi->getInvoice($this->tenantId, $paymentData['invoiceId'])->getInvoices()[0];
            $account = $this->accountingApi->getAccount($this->tenantId, $paymentData['accountId'])->getInvoices()[0];

            $payment = new Payment();
            $payment->setInvoice($invoice);
            $payment->setAmount($paymentData['amount']);
            $payment->setDate(new \DateTime());
            $payment->setAccount($account);
            $payment->setCurrencyRate(1);
            $payment->setStatus(Payment::STATUS_AUTHORISED);
            $resultingPayment = $this->accountingApi->createPayment($this->tenantId, $payment);
            return $resultingPayment->getPayments()[0];

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create payment: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create payment: " . $e->getMessage());
        }
    }

    public function storeVendorCredit($companyId, $vendorCreditData)
    {
        try {
            $this->getAccessToken($companyId);

            $vendor = $this->accountingApi->getContact($this->tenantId, $vendorCreditData['vendorID'])->getContacts();

            $creditDate = new DateTime($vendorCreditData['txnDate']);
            $dueDate = (clone $creditDate)->modify('+3 days');

            Log::info('Credit Date: ' . $creditDate->format('Y-m-d') . ' Due Date: ' . $dueDate->format('Y-m-d'));

            $creditNote = new CreditNote();

            $creditNote->setType(CreditNote::TYPE_ACCPAYCREDIT)
                ->setContact($vendor[0])
                ->setDate($creditDate->format('Y-m-d'))
                ->setDueDate($dueDate->format('Y-m-d'))
                ->setCreditNoteNumber($vendorCreditData['invoiceNo'])
                ->setReference($vendorCreditData['description'])
                ->setCurrencyCode($vendorCreditData['currencyCode'])
                ->setLineAmountTypes($vendorCreditData['globalTaxCalculation'])
                ->setStatus(CreditNote::STATUS_AUTHORISED);

            $lineItem = new LineItem();
            $lineItem->setDescription($vendorCreditData['description'])
                ->setQuantity(1)
                ->setUnitAmount($vendorCreditData['amount'])
                ->setAccountCode($vendorCreditData['accountCode'])
                ->setTaxType($vendorCreditData['taxCodeRef']);

            $creditNote->setLineItems([$lineItem]);

            $creditNotes = new \XeroAPI\XeroPHP\Models\Accounting\CreditNotes();
            $creditNotes->setCreditNotes([$creditNote]);

            $resultingCreditNote = $this->accountingApi->createCreditNotes($this->tenantId, $creditNotes);

            $xeroCreditId = $resultingCreditNote[0];

            Log::info('Xero Credit ID: ' . $xeroCreditId);

            return $xeroCreditId;

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to create vendor credit: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create vendor credit: " . $e->getMessage());
        }
    }

    public function updateVendorCredit($companyId, $vendorCreditId, $vendorCreditData)
    {
        try {
            $this->getAccessToken($companyId);

            $existingCreditNote = $this->accountingApi->getCreditNote($this->tenantId, $vendorCreditId)->getCreditNotes()[0];

            $vendor = $this->accountingApi->getContact($this->tenantId, $vendorCreditData['vendorID'])->getContacts()[0];

            $existingCreditNote->setContact($vendor)
                ->setDate(new \DateTime($vendorCreditData['txnDate'] ?? $existingCreditNote->getDate()))
                ->setCreditNoteNumber($vendorCreditData['invoiceNo'] ?? $existingCreditNote->getCreditNoteNumber())
                ->setReference($vendorCreditData['description'] ?? $existingCreditNote->getReference())
                ->setCurrencyCode($vendorCreditData['currencyCode'] ?? $existingCreditNote->getCurrencyCode()) // Keep existing if not provided
                ->setLineAmountTypes('Inclusive') 
                ->setStatus(CreditNote::STATUS_AUTHORISED);

            $lineItem = new LineItem();
            $lineItem->setDescription($vendorCreditData['description'] ?? $existingCreditNote->getLineItems()[0]->getDescription())
                ->setQuantity(1)
                ->setUnitAmount($vendorCreditData['amount'] ?? $existingCreditNote->getLineItems()[0]->getUnitAmount())
                ->setAccountCode($vendorCreditData['accountCode'] ?? $existingCreditNote->getLineItems()[0]->getAccountCode())
                ->setTaxType($vendorCreditData['taxCodeRef'] ?? $existingCreditNote->getLineItems()[0]->getTaxType());

            $existingCreditNote->setLineItems([$lineItem]);

            $creditNotes = new CreditNotes();
            $creditNotes->setCreditNotes([$existingCreditNote]);

            $updatedCreditNote = $this->accountingApi->updateCreditNote($this->tenantId, $vendorCreditId, $creditNotes);
            return $updatedCreditNote->getCreditNotes()[0];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Failed to update vendor credit: " . $errorBody);
        } catch (\Exception $e) {
            throw new \Exception("Failed to update vendor credit: " . $e->getMessage());
        }
    }

    public function getAllExpenseAccount($companyId)
    {
        try {
            $this->getAccessToken($companyId);

            $filter = 'Type == "EXPENSE"';
            $accounts = $this->accountingApi->getAccounts($this->tenantId, null, $filter)->getAccounts();
            return $accounts;

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Error fetching accounts: " . $errorBody);
        }
    }

    public function getAllAccount($companyId)
    {
        try {

            $this->getAccessToken($companyId);

            $filter = 'EnablePaymentsToAccount == true';
            
            $accounts = $this->accountingApi->getAccounts($this->tenantId,null, $filter);

            return $accounts;

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errorBody = $e->getResponseBody();
            throw new \Exception("Error fetching accounts: " . $errorBody);
        }
    }

    public function getAllCustomers($companyId)
    {
        return $companyId;
    }

    public function getProjectByName($companyId, $name)
    {
        return $companyId;
    }

    public function storeClass($companyId, $projectData)
    {
        return $companyId;
    }

    public function getVendorById($companyId, $vendorId)
    {
        return $companyId;
    }

    public function saveSaleReceiptPdf($companyId, $invoiceId)
    {
        return $companyId;
    }

    public function getBillByCompanyId($companyId)
    {
        return $companyId;
    }

    public function getInvoiceByCompanyId($companyId)
    {
        return $companyId;
    }

    public function getSaleReceiptByCompanyId($companyId)
    {
        return $companyId;
    }

    public function storeCreditMemo(int $companyId, array $creditMemoData)
    {
        return $companyId;
    }

    public function saveCreditNotePdf(int $companyId, string $creditNoteId)
    {
        return $companyId;
    }
}
