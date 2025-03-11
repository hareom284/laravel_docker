<?php

namespace Src\Company\CompanyManagement\Domain\Services;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use QuickBooksOnline\API\Facades\Bill;
use Illuminate\Support\Facades\Storage;
use QuickBooksOnline\API\Facades\Vendor;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Payment;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\BillPayment;
use QuickBooksOnline\API\Facades\VendorCredit;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\ServiceException;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Facades\CreditMemo;
use QuickBooksOnline\API\Facades\QuickBookClass;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QuickBookEloquentModel;

class QuickbookService implements AccountingServiceInterface
{
    private $dataService;
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $baseUrl;
    private $realmId;
    private $accessToken;
    private $refreshToken;

    private function loadSettings(): void
    {
        $settings = GeneralSettingEloquentModel::whereIn('setting', [  
                'quickbook_client_id',
                'quickbook_client_secret',
                'quickbook_redirect_uri',
                'quickbook_base_url'
            ])->pluck('value', 'setting');

        $this->clientId = $settings['quickbook_client_id'];
        $this->clientSecret = $settings['quickbook_client_secret'];
        $this->redirectUri = $settings['quickbook_redirect_uri'];
        $this->baseUrl = $settings['quickbook_base_url'];
    }

    private function getQuickbookRecord($companyId): QuickBookEloquentModel
    {
        return QuickBookEloquentModel::where('company_id', $companyId)->first();
    }

    public function getAccessToken($companyId): void
    {
        try {

            $this->loadSettings();

            $quickbookRecord = $this->getQuickbookRecord($companyId);

            $originalRefreshToken = $quickbookRecord->refresh_token;
            $this->realmId = $quickbookRecord->accounting_software_company_id;

            $oauth2LoginHelper = new OAuth2LoginHelper($this->clientId, $this->clientSecret);
            $accessTokenObj = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($originalRefreshToken);

            $this->accessToken = $accessTokenObj->getAccessToken();
            $this->refreshToken = $accessTokenObj->getRefreshToken();

            if ($originalRefreshToken !== $this->refreshToken) {
                $quickbookRecord->refresh_token = $this->refreshToken;
                $quickbookRecord->save();
            }

            $this->dataService = DataService::Configure([
                'auth_mode'       => 'oauth2',
                'ClientID'        => $this->clientId,
                'ClientSecret'    => $this->clientSecret,
                'RedirectURI'     => $this->redirectUri,
                'accessTokenKey'  => $this->accessToken,
                'refreshTokenKey' => $this->refreshToken,
                'QBORealmID'      => $this->realmId,
                'baseUrl'         => $this->baseUrl,
            ]);
        } catch (Exception $e) {
            throw new Exception('Error obtaining access token.');
        }
    }

    public function getCustomer($companyId, $name)
    {
        $this->getAccessToken($companyId);

        $customer = $this->dataService->Query("SELECT * FROM Customer WHERE FullyQualifiedName = '$name'" );

        if (isset($customer) && !empty($customer) && count($customer) > 0) {

            return $customer[0]->Id;
        }else{

            return null;
        }
    }

    public function getCustomerById($companyId,$id)
    {
        $this->getAccessToken($companyId);

        // Get the customer by QBO ID
        $customer = $this->dataService->FindById('Customer', $id);

        // Check if customer exists
        if (!$customer) {
            throw new Exception('Customer not found');
        }

        return $customer;
    }

    public function getAllCustomers($companyId)
    {
        try {
            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $customers = $this->dataService->Query("SELECT * FROM Customer");

            if (!$customers) {

                $error = $this->dataService->getLastError();

                throw new \Exception("Error fetching customers: " . $error->getResponseBody());

            } else {

                return $customers;
            }
        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch customers from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    //Get Or Create customer to quickbook api
    public function storeCustomer($companyId,$customer_data)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $displayname = $customer_data['name'];

        $companyName = $customer_data['companyName'];

        $theResourceObj = Customer::create([
            "DisplayName" =>  $displayname,
            "FullyQualifiedName" => $displayname,
            "CompanyName" => $companyName,
            "PrimaryEmailAddr" => [
                "Address" => $customer_data['email']
            ],
            "BillAddr" => [
                "Line1" => $customer_data['address'],
                "City" => "SINGAPORE",
                "Country" => "SINGAPORE",
                "PostalCode" => $customer_data['postal_code']
            ],
            "PrimaryPhone" => [
                "FreeFormNumber" => $customer_data['contact_no']
            ],
            "Mobile" => [
                "FreeFormNumber" => $customer_data['contact_no']
            ],

        ]);

        try {

            $result = $this->dataService->Add($theResourceObj);
            //Log::info('customerId : ',$result);
            return $result->Id;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function updateCustomer($companyId,$customerId, $customer_data)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        // Get the customer to update
        $customer = $this->dataService->FindById('Customer', $customerId);

        // Check if customer exists
        if (!$customer) {
            throw new Exception('Customer not found');
        }
        // Prepare the update data array
        $updateData = [];

        if (isset($customer_data['name'])) {
            $updateData['DisplayName'] = $customer_data['name'];
            $updateData['FullyQualifiedName'] = $customer_data['name'];
        }
        if (isset($customer_data['companyName'])) {
            $updateData['CompanyName'] = $customer_data['companyName'];
        }
        if (isset($customer_data['email'])) {
            $updateData['PrimaryEmailAddr'] = [
                "Address" => $customer_data['email']
            ];
        }
        if (isset($customer_data['address'])) {
            $updateData['BillAddr'] = [
                "Line1" => $customer_data['address'],
                "City" => "SINGAPORE",
                "Country" => "SINGAPORE",
                "PostalCode" => $customer_data['postal_code']
            ];
        }
        if (isset($customer_data['contact_no'])) {
            $updateData['PrimaryPhone'] = [
                "FreeFormNumber" => $customer_data['contact_no']
            ];
            $updateData['Mobile'] = [
                "FreeFormNumber" => $customer_data['contact_no']
            ];
        }

        $theResourceObj = Customer::update($customer, $updateData);

        try {
            $resultingObj = $this->dataService->Update($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function getAllVendors($companyId)
    {
        try {
            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $vendors = $this->dataService->Query("SELECT Id, DisplayName FROM Vendor MAXRESULTS 500");

            if (!$vendors) {

                $error = $this->dataService->getLastError();

                throw new \Exception("Error fetching vendors: " . $error->getResponseBody());

            } else {

                return $vendors;
            }
        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch vendors from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function getVendorByName($companyId,$name)
    {
        try {
            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $vendor = $this->dataService->Query("SELECT * FROM Vendor WHERE DisplayName = '$name'" );

            if (isset($vendor) && !empty($vendor) && count($vendor) > 0) {

                return $vendor[0];
            }else{

                return null;
            }

        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch vendors from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function getVendorById($companyId,$vendorId)
    {
        try {
            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $vendor = $this->dataService->FindbyId('vendor', $vendorId);

            return $vendor;

        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch vendors from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function storeVendor($companyId,$vendorData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $displayname = $vendorData['name'];
        $contactNo = $vendorData['contact_no'];
        $email = $vendorData['email'];
        $streetName = $vendorData['street_name'];
        $postalCode = $vendorData['postal_code'];

		//Add a new Vendor
		$theResourceObj = Vendor::create([
		    "BillAddr" => [
		        "Line1" => $streetName,
		        "City" => "Singapore",
		        "Country" => "Singapore",
		        "CountrySubDivisionCode" => "SG",
		        "PostalCode" => $postalCode
		    ],
		    "CompanyName" => $displayname,
		    "DisplayName" => $displayname,
		    "PrintOnCheckName" => $displayname,
		    "PrimaryPhone" => [
		        "FreeFormNumber" => $contactNo
		    ],
		    "Mobile" => [
		        "FreeFormNumber" => $contactNo
		    ],
		    "PrimaryEmailAddr" => [
		        "Address" => $email
		    ],
		]);

		try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj->Id;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function updateVendor($companyId,$vendorData,$id)
    {
        $this->getAccessToken($companyId);

        $displayname = $vendorData['name'];
        $contactNo = $vendorData['contact_no'];
        $email = $vendorData['email'];
        $streetName = $vendorData['street_name'];
        $postalCode = $vendorData['postal_code'];

        $contactPersonName = explode( '-', $vendorData['contact_person'] ); 

        $vendor = $this->dataService->FindbyId('vendor', $id);

        $theResourceObj = Vendor::update($vendor , [
            "BillAddr" => [
                "Line1" => $streetName,
                "City" => "Singapore",
                "Country" => "Singapore",
                "CountrySubDivisionCode" => "SG",
                "PostalCode" => $postalCode
            ],
            "Title" => $contactPersonName[0],
            "GivenName" =>  $contactPersonName[1],
            "FamilyName" =>  $contactPersonName[2],
            "CompanyName" => $displayname,
            "DisplayName" => $displayname,
            "PrintOnCheckName" => $displayname,
            "PrimaryPhone" => [
                "FreeFormNumber" => $contactNo
            ],
            "Mobile" => [
                "FreeFormNumber" => $contactNo
            ],
            "PrimaryEmailAddr" => [
                "Address" => $email
            ],
        ]);

        try {
            $resultingObj = $this->dataService->Update($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function getInvoiceByCustomerId($companyId,$customerId)
    {
        try {

            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $invoices = $this->dataService->Query("SELECT * FROM Invoice WHERE CustomerRef = '$customerId'");

            if (isset($invoices) && !empty($invoices) && count($invoices) > 0) {

                return $invoices;
            }else{

                return null;
            }

        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch invoices from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function storeInvoice($companyId,$invoiceData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        if($this->baseUrl == 'development'){

            $theResourceObj = Invoice::create([
                "PrivateNote" => $invoiceData['name'],
                "TxnDate" => $invoiceData['invoiceDate'],
                "Line" => [
                    [
                        "Id" => 1,
                        "Amount" => $invoiceData['netAmountTaxable'],
                        "Description" => $invoiceData['name'],
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => [
                            "ItemRef" => [
                                'value' => $invoiceData['itemRef'],
                            ],
                            "TaxInclusiveAmt" => $invoiceData['taxInclusiveAmt'],
                            "ClassRef" => [
                                'value' => $invoiceData['classRef']
                            ]
                        ]
                    ]
                ],
                "CustomerMemo" => [
                    "value" => $invoiceData['remark'],
                ],
                "CustomerRef"=> [
                    "value"=> $invoiceData['customerId']
                ],
            ]);
        }else{

            if($invoiceData['isGstEnable']){
                $theResourceObj = Invoice::create([
                    "PrivateNote" => $invoiceData['name'],
                    "TxnDate" => $invoiceData['invoiceDate'],
                    "Line" => [
                        [
                            "Id" => 1,
                            "Amount" => $invoiceData['netAmountTaxable'],
                            "Description" => $invoiceData['name'],
                            "DetailType" => "SalesItemLineDetail",
                            "SalesItemLineDetail" => [
                                "ItemRef" => [
                                    'value' => $invoiceData['itemRef'],
                                ],
                                "TaxCodeRef" => [
                                    'value' => $invoiceData['taxCodeRef'], // Assuming 'taxCodeRef' is part of $invoiceData
                                ],
                                "TaxInclusiveAmt" => $invoiceData['taxInclusiveAmt'], // Assuming 'taxInclusiveAmt' is part of $invoiceData
                                "ClassRef" => [
                                    'value' => $invoiceData['classRef']
                                ]
                            ]
                        ]
                    ],
                    "CustomerMemo" => [
                        "value" => $invoiceData['remark'],
                    ],
                    "CustomerRef"=> [
                        "value"=> $invoiceData['customerId']
                    ],
                    "TxnTaxDetail" => [
                        "TotalTax" => $invoiceData['totalTax'], // Assuming 'totalTax' is part of $invoiceData
                        "TaxLine" => [
                            "Amount" => $invoiceData['totalTax'],
                            "DetailType" => "TaxLineDetail",
                            "TaxLineDetail" => [
                                "TaxRateRef" => $invoiceData['taxRateRef'],
                                "PercentBased" => "true",
                                "TaxPercent" => $invoiceData['TaxPercent'],
                                "NetAmountTaxable" => $invoiceData['netAmountTaxable'],
                            ]
                        ]
    
                    ]
                ]);
            }else{

                $theResourceObj = Invoice::create([
                    "PrivateNote" => $invoiceData['name'],
                    "TxnDate" => $invoiceData['invoiceDate'],
                    "Line" => [
                        [
                            "Id" => 1,
                            "Amount" => $invoiceData['netAmountTaxable'],
                            "Description" => $invoiceData['name'],
                            "DetailType" => "SalesItemLineDetail",
                            "SalesItemLineDetail" => [
                                "ItemRef" => [
                                    'value' => $invoiceData['itemRef'],
                                ],
                                "TaxInclusiveAmt" => $invoiceData['taxInclusiveAmt'], // Assuming 'taxInclusiveAmt' is part of $invoiceData
                                "ClassRef" => [
                                    'value' => $invoiceData['classRef']
                                ]
                            ]
                        ]
                    ],
                    "CustomerMemo" => [
                        "value" => $invoiceData['remark'],
                    ],
                    "CustomerRef"=> [
                        "value"=> $invoiceData['customerId']
                    ]
                ]);
            }
        }

        try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function updateInvoice($companyId,$invoiceData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $invoice = $this->dataService->FindbyId('invoice', $invoiceData['quickBookId']);

        if($invoiceData['isGstEnable']){

            $theResourceObj = Invoice::update($invoice, [
                "TxnDate" => $invoiceData['invoiceDate'],
                "PrivateNote" => $invoiceData['name'],
                "Line" => [
                    [
                        "Id" => 1,
                        "Amount" => $invoiceData['netAmountTaxable'],
                        "Description" => $invoiceData['name'],
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => [
                            "ItemRef" => [
                                'value' => $invoiceData['itemRef'],
                            ],
                            "TaxCodeRef" => [
                                'value' => $invoiceData['taxCodeRef'], // Assuming 'taxCodeRef' is part of $invoiceData
                            ],
                            "TaxInclusiveAmt" => $invoiceData['taxInclusiveAmt'], // Assuming 'taxInclusiveAmt' is part of $invoiceData
                            "ClassRef" => [
                                'value' => $invoiceData['classRef']
                            ]
                        ]
                    ]
                ],
                "CustomerMemo" => [
                    "value" => $invoiceData['remark'],
                ],
                "CustomerRef"=> [
                      "value"=> $invoiceData['customerId']
                ],
                "TxnTaxDetail" => [
                    "TotalTax" => $invoiceData['totalTax'], // Assuming 'totalTax' is part of $invoiceData
                    "TaxLine" => [
                        "Amount" => $invoiceData['totalTax'],
                        "DetailType" => "TaxLineDetail",
                        "TaxLineDetail" => [
                            "TaxRateRef" => $invoiceData['taxRateRef'],
                            "PercentBased" => "true",
                            "TaxPercent" => "9",
                            "NetAmountTaxable" => $invoiceData['netAmountTaxable'],
                        ]
                    ]
    
                ]
            ]);

        }else{

            $theResourceObj = Invoice::update($invoice, [
                "TxnDate" => $invoiceData['invoiceDate'],
                "PrivateNote" => $invoiceData['name'],
                "Line" => [
                    [
                        "Id" => 1,
                        "Amount" => $invoiceData['netAmountTaxable'],
                        "Description" => $invoiceData['name'],
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => [
                            "ItemRef" => [
                                'value' => $invoiceData['itemRef'],
                            ],
                            "TaxInclusiveAmt" => $invoiceData['taxInclusiveAmt'], // Assuming 'taxInclusiveAmt' is part of $invoiceData
                            "ClassRef" => [
                                'value' => $invoiceData['classRef']
                            ]
                        ]
                    ]
                ],
                "CustomerMemo" => [
                    "value" => $invoiceData['remark'],
                ],
                "CustomerRef"=> [
                      "value"=> $invoiceData['customerId']
                ]
            ]);
        }

        try {

            $resultingObj = $this->dataService->Update($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {

            throw new Exception($ex->getMessage());
        }
    }

    public function saveInvoicePdf($companyId,$invoiceId)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        try {

            // Find the invoice by ID
            $invoice = $this->dataService->FindbyId('invoice', $invoiceId);

            $filePath = storage_path('app/public/QBO');

            if (!Storage::exists('public/QBO')) {
                Storage::makeDirectory('public/QBO');
            }

            $directoryForThePDF = $this->dataService->DownloadPDF($invoice, $filePath);

            $result = preg_replace('/^.*QBO\//', '', $directoryForThePDF);

            return $result;

        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function saveSaleReceiptPdf($companyId,$invoiceId)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        try {

            // Find the invoice by ID
            $invoice = $this->dataService->FindbyId('salesreceipt', $invoiceId);

            $filePath = storage_path('app/public/QBO');

            if (!Storage::exists('public/QBO')) {
                Storage::makeDirectory('public/QBO');
            }

            $directoryForThePDF = $this->dataService->DownloadPDF($invoice, $filePath);

            $result = preg_replace('/^.*QBO\//', '', $directoryForThePDF);

            return $result;

        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function storePayment($companyId,$paymentData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        if(is_null($paymentData['DepositToAccountRef'])){
            $theResourceObj = Payment::create([
                "TxnDate" => $paymentData['invoiceDate'],
                "CustomerRef" =>
                [
                  "value" => $paymentData['customerId']
                ],
                "TotalAmt" => $paymentData['amount'],
                "Line" => [
                [
                    "Amount" => $paymentData['amount'],
                    "LinkedTxn" => [
                    [
                      "TxnId" => $paymentData['invoiceId'],
                      "TxnType" => "Invoice"
                    ]]
                ]],
                "CurrencyRef" => 
                [
                    "value" => "SGD",
                    "name" => "Singapore Dollar"
                ]
            ]);
        }else{
            $theResourceObj = Payment::create([
                "TxnDate" => $paymentData['invoiceDate'],
                "CustomerRef" =>
                [
                  "value" => $paymentData['customerId']
                ],
                "DepositToAccountRef" =>
                [
                  "value" => $paymentData['DepositToAccountRef']
                ],
                "TotalAmt" => $paymentData['amount'],
                "Line" => [
                [
                    "Amount" => $paymentData['amount'],
                    "LinkedTxn" => [
                    [
                      "TxnId" => $paymentData['invoiceId'],
                      "TxnType" => "Invoice"
                    ]]
                ]],
                "CurrencyRef" => 
                [
                    "value" => "SGD",
                    "name" => "Singapore Dollar"
                ]
            ]);
        }

        try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function updatePayment($paymentData)
    {
        $this->getAccessToken(1);

        $this->dataService->throwExceptionOnError(true);

        $payment = $this->dataService->FindbyId('payment', $paymentData['quickBookId']);

        $theResourceObj = Payment::update($payment , [
            "TotalAmt" => $paymentData['amount'],
            "Line" => [
            [
                "Id" => 1,
                "Amount" => $paymentData['amount'],
                "LinkedTxn" => [
                [
                  "TxnId" => $paymentData['invoiceId'],
                  "TxnType" => "Invoice"
                ]]
            ]],
            "CurrencyRef" => 
            [
                "value" => "SGD",
                "name" => "Singapore Dollar"
            ]

        ]);

        try {

            $resultingObj = $this->dataService->Update($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function storeBill($companyId,$billData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        if($this->baseUrl == 'development'){

            $theResourceObj = Bill::create([
                "DocNumber" => $billData['invoiceNo'],
                "PrivateNote" => $billData['description'],
                "TxnDate" => $billData['invoiceDate'],
                "Line" =>
                [
                    [
                        "Id" => "1",
                        "Amount" => $billData['amount'],
                        "Description" => $billData['description'], 
                        "DetailType" => "AccountBasedExpenseLineDetail",
                        "AccountBasedExpenseLineDetail" =>
                        [
                            "AccountRef" => 
                            [
                                "value" => 80
                            ],
                            "CustomerRef" => 
                            [
                                "value" => $billData['userID']
                            ],
                            "ClassRef" => 
                            [
                                "value" => $billData['classRef']
                            ],
                        ]
                    ]
                ],
                "VendorRef" =>
                [
                    "value" =>$billData['vendorID']
                ],
                "TotalAmt" => $billData['totalAmount'],
                "Balance" => $billData['totalAmount'],
            ]);
        }else{

            if($billData['isGstEnable']){

                if($billData['isRebateAmountEnable']){

                    $createData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => $billData['taxCodeRef'], 
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                ]
                            ],
                            [
                                "Id" => "2",
                                "Amount" => $billData['secondLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['rebateCategoryRef']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => $billData['taxCodeRef'], 
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                ]
                            ]
                        ],
                        "VendorRef" =>
                        [
                            "value" =>$billData['vendorID']
                        ],
                        "TxnTaxDetail" => [
                            "TotalTax" => $billData['totalTax'],
                            "TaxLine" => [
                                "Amount" => $billData['totalTax'],
                                "DetailType" => "TaxLineDetail",
                                "TaxLineDetail" => [
                                    "TaxRateRef" => $billData['taxRateRef'],
                                    "PercentBased" => "true",
                                    "TaxPercent" => $billData['taxPercent'],
                                    "NetAmountTaxable" => $billData['netAmountTaxable'],
                                ]
                            ]
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "GlobalTaxCalculation" => $billData['globalTaxCalculation'],
                        "Balance" => $billData['totalAmount'],
                    ];

                }else{

                    $createData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => $billData['taxCodeRef'], 
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                ]
                            ]
                        ],
                        "VendorRef" =>
                        [
                            "value" =>$billData['vendorID']
                        ],
                        "TxnTaxDetail" => [
                            "TotalTax" => $billData['totalTax'],
                            "TaxLine" => [
                                "Amount" => $billData['totalTax'],
                                "DetailType" => "TaxLineDetail",
                                "TaxLineDetail" => [
                                    "TaxRateRef" => $billData['taxRateRef'],
                                    "PercentBased" => "true",
                                    "TaxPercent" => $billData['taxPercent'],
                                    "NetAmountTaxable" => $billData['netAmountTaxable'],
                                ]
                            ]
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "GlobalTaxCalculation" => $billData['globalTaxCalculation'],
                        "Balance" => $billData['totalAmount'],
                    ];

                }
            }else{

                if($billData['isRebateAmountEnable']){

                    $createData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                       "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],    
                                    "TaxCodeRef" => [
                                        'value' => 'NON', 
                                    ],                         
                                ]
                            ],
                            [
                                "Id" => "2",
                                "Amount" => $billData['secondLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['rebateCategoryRef']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => 'NON', 
                                    ],   
                                ]
                            ]
                        ],
                        "VendorRef" =>
                        [
                            "value" =>$billData['vendorID']
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "Balance" => $billData['totalAmount'],
                    ];

                }else{

                    $createData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                       "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => 'NON', 
                                    ],   
                                ]
                            ]
                        ],
                        "VendorRef" =>
                        [
                            "value" =>$billData['vendorID']
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "Balance" => $billData['totalAmount'],
                    ];
                }

                if($billData['isTaxCalculationNeeded']){
                    $createData['GlobalTaxCalculation'] = $billData['globalTaxCalculation'];
                }
            }
        }

        Log::info('Create Bill Data Befor Send To QBO:', $createData);

        $theResourceObj = Bill::create($createData);

        try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj->Id;
            
        } catch (ServiceException $ex) {
            
            throw new Exception($ex->getMessage());
        }
    }

    public function updateBill($companyId,$billData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->setLogLocation(storage_path('logs/quickbooks.log')); 
        $this->dataService->throwExceptionOnError(true);

        // Retrieve the existing bill
        $existingBill = $this->dataService->FindById('bill', $billData['billID']);

        if($this->baseUrl == 'development'){

            // Update the bill with new data
            $theResourceObj = Bill::update($existingBill, [
                "DocNumber" => $billData['invoiceNo'],
                "PrivateNote" => $billData['description'],
                "TxnDate" => $billData['invoiceDate'],
                "Line" =>
                [
                    [
                        "Id" => "1",
                        "Amount" => $billData['amount'],
                        "Description" => $billData['description'], 
                        "DetailType" => "AccountBasedExpenseLineDetail",
                        "AccountBasedExpenseLineDetail" =>
                        [
                            "AccountRef" => 
                            [
                                "value" => $billData['quickBookExpenseID']
                            ]
                        ]
                    ]
                ],
                "VendorRef" =>
                [
                    "value" => $billData['vendorID']
                ],
                "TotalAmt" => $billData['totalAmount'],
                "Balance" => $billData['totalAmount'],
            ]);

        }else{

            if($billData['isGstEnable']){

                if($billData['isRebateAmountEnable']){
                    $qboUpdateData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => $billData['taxCodeRef'], 
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                ]
                            ],
                            [
                                "Id" => "2",
                                "Amount" => $billData['secondLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['rebateCategoryRef']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => $billData['taxCodeRef'], 
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                ]
                            ] 
                        ],
                        "VendorRef" =>
                        [
                            "value" => $billData['vendorID']
                        ],
                        "TxnTaxDetail" => [
                            "TotalTax" => $billData['totalTax'], // Assuming 'totalTax' is part of $invoiceData
                            "TaxLine" => [
                                "Amount" => $billData['totalTax'],
                                "DetailType" => "TaxLineDetail",
                                "TaxLineDetail" => [
                                    "TaxRateRef" => $billData['taxRateRef'],
                                    "PercentBased" => "true",
                                    "TaxPercent" => $billData['taxPercent'],
                                    "NetAmountTaxable" => $billData['netAmountTaxable'],
                                ]
                            ]
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "GlobalTaxCalculation" => $billData['globalTaxCalculation'],
                        "Balance" => $billData['totalAmount'],
                    ];

                }else{
                    $qboUpdateData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => $billData['taxCodeRef'], 
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                ]
                            ]
                        ],
                        "VendorRef" =>
                        [
                            "value" => $billData['vendorID']
                        ],
                        "TxnTaxDetail" => [
                            "TotalTax" => $billData['totalTax'], // Assuming 'totalTax' is part of $invoiceData
                            "TaxLine" => [
                                "Amount" => $billData['totalTax'],
                                "DetailType" => "TaxLineDetail",
                                "TaxLineDetail" => [
                                    "TaxRateRef" => $billData['taxRateRef'],
                                    "PercentBased" => "true",
                                    "TaxPercent" => $billData['taxPercent'],
                                    "NetAmountTaxable" => $billData['netAmountTaxable'],
                                ]
                            ]
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "GlobalTaxCalculation" => $billData['globalTaxCalculation'],
                        "Balance" => $billData['totalAmount'],
                    ];
                }
            }else{
                if($billData['isRebateAmountEnable']){
                    
                    $qboUpdateData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => 'NON', 
                                    ],   
                                ]
                            ],
                            [
                                "Id" => "2",
                                "Amount" => $billData['secondLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['rebateCategoryRef']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => 'NON', 
                                    ],   
                                ]
                            ]

                        ],
                        "VendorRef" =>
                        [
                            "value" => $billData['vendorID']
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "Balance" => $billData['totalAmount'],
                    ];

                }else{

                    $qboUpdateData = [
                        "DocNumber" => $billData['invoiceNo'],
                        "PrivateNote" => $billData['description'],
                        "TxnDate" => $billData['invoiceDate'],
                        "Line" =>
                        [
                            [
                                "Id" => "1",
                                "Amount" => $billData['firtLineItemAmount'],
                                "Description" => $billData['description'], 
                                "DetailType" => "AccountBasedExpenseLineDetail",
                                "AccountBasedExpenseLineDetail" =>
                                [
                                    "AccountRef" => 
                                    [
                                        "value" => $billData['quickBookExpenseID']
                                    ],
                                    "CustomerRef" => 
                                    [
                                        "value" => $billData['userID']
                                    ],
                                    "ClassRef" => 
                                    [
                                        "value" => $billData['classRef']
                                    ],
                                    "TaxCodeRef" => [
                                        'value' => 'NON', 
                                    ],   
                                ]
                            ]
                        ],
                        "VendorRef" =>
                        [
                            "value" => $billData['vendorID']
                        ],
                        "TotalAmt" => $billData['totalAmount'],
                        "Balance" => $billData['totalAmount'],
                    ];
                }  

                if($billData['isTaxCalculationNeeded']){
                    $qboUpdateData['GlobalTaxCalculation'] = $billData['globalTaxCalculation'];
                }
            }
        }

        Log::info('Update Bill Data Befor Send To QBO:', $qboUpdateData);

        $theResourceObj = Bill::update($existingBill, $qboUpdateData);
        
        try {
            $resultingObj = $this->dataService->Update($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function storeBillPayment($companyId,$billPaymentData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $theResourceObj = BillPayment::create($billPaymentData);

        try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function storeVendorCredit($companyId,$vendorCreditData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $theResourceObj = VendorCredit::create([
            "TxnDate" => $vendorCreditData['txnDate'],
            "DocNumber" => $vendorCreditData['invoiceNo'],
            "PrivateNote" => $vendorCreditData['description'],
            "Line" => [
                [
                    "Id" =>"1",
                    "Amount" => $vendorCreditData['amount'],
                    "Description" => $vendorCreditData['description'],
                    "DetailType" => "AccountBasedExpenseLineDetail",
                    "AccountBasedExpenseLineDetail" =>
                    [
                        "AccountRef" =>
                        [
                            "value" =>"72",
                        ],
                        "BillableStatus" => "NotBillable",
                        "TaxCodeRef" =>
                        [
                            "value" => $vendorCreditData['taxCodeRef']
                        ]
                    ]
                ]
            ],
            "TxnTaxDetail" => [
                "TotalTax" => $vendorCreditData['gstValue'],
                "TaxLine" => [
                    "Amount" => $vendorCreditData['gstValue'],
                    "DetailType" => "TaxLineDetail",
                    "TaxLineDetail" => [
                        "TaxRateRef" => $vendorCreditData['taxRateRef'],
                        "PercentBased" => "true",
                        "TaxPercent" => $vendorCreditData['taxPercent'],
                        "NetAmountTaxable" => $vendorCreditData['amount'],
                    ]
                ]

            ],
            "VendorRef" =>
            [
                "value" => $vendorCreditData['vendorID'],
            ],
            "APAccountRef" =>
            [
                "value" =>"70",
            ],
            "TotalAmt" => $vendorCreditData['totalAmount'],
            "Balance" => $vendorCreditData['totalAmount'],
        ]);

        try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            
            throw new Exception($ex->getMessage());
        }
    }

    public function updateVendorCredit($companyId,$vendorCreditId, $vendorCreditData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        // Retrieve the existing VendorCredit by ID
        $existingVendorCredit = $this->dataService->FindbyId('vendorcredit', $vendorCreditId);

        if (!$existingVendorCredit) {
            throw new \Exception("QBO Vendor Credit not found with ID: " . $vendorCreditId);
        }

        // Update the VendorCredit with new data
        $theResourceObj = VendorCredit::update($existingVendorCredit, [
            "TxnDate" => $vendorCreditData['txnDate'],
            "PrivateNote" => $vendorCreditData['description'],
            "DocNumber" => $vendorCreditData['invoiceNo'],
            "sparse" => true,
            "Line" => [
                [
                    "Id" => "1",
                    "Amount" => $vendorCreditData['amount'],
                    "Description" => $vendorCreditData['description'],
                    "DetailType" => "AccountBasedExpenseLineDetail",
                    "AccountBasedExpenseLineDetail" => [
                        "AccountRef" => [
                            "value" => "72",
                        ],
                        "BillableStatus" => "NotBillable",
                        "TaxCodeRef" => [
                            "value" => $vendorCreditData['taxCodeRef'],
                        ]
                    ]
                ]
            ],
            "TxnTaxDetail" => [
                "TotalTax" => $vendorCreditData['gstValue'],
                "TaxLine" => [
                    "Amount" => $vendorCreditData['gstValue'],
                    "DetailType" => "TaxLineDetail",
                    "TaxLineDetail" => [
                        "TaxRateRef" => $vendorCreditData['taxRateRef'],
                        "PercentBased" => "true",
                        "TaxPercent" => $vendorCreditData['taxPercent'],
                        "NetAmountTaxable" => $vendorCreditData['amount'],
                    ]
                ]
            ],
            "VendorRef" => [
                "value" => $vendorCreditData['vendorID'],
            ],
            "APAccountRef" => [
                "value" => "70",
            ],
            "TotalAmt" => $vendorCreditData['totalAmount'],
            "GlobalTaxCalculation" => $vendorCreditData['globalTaxCalculation'],
            "Balance" => $vendorCreditData['totalAmount'],
        ]);

        try {
            // Save the updated VendorCredit to QuickBooks
            $resultingVendorCreditObj = $this->dataService->Update($theResourceObj);

            return $resultingVendorCreditObj;
            
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }       
    }

    public function getAllAccount($companyId)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $accounts = $this->dataService->Query("SELECT * FROM Account WHERE AccountType = 'Bank' And AccountSubType = 'Checking' ");

        if (!$accounts) {

            $error = $this->dataService->getLastError();

            throw new \Exception("Error fetching accounts: " . $error);

        } else {

            return $accounts;
        }
    }

    public function getAllExpenseAccount($companyId)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $accounts = $this->dataService->Query("SELECT * FROM Account WHERE AccountType IN ('Cost of Goods Sold', 'Expense')");

        if (!$accounts) {

            $error = $this->dataService->getLastError();

            throw new \Exception("Error fetching accounts: " . $error);

        } else {

            return $accounts;
        }
    }

    public function getProjectByName($companyId, $name)
    {

        try {
            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $project = $this->dataService->Query("SELECT * FROM Class WHERE FullyQualifiedName = '$name'" );

            if (isset($project) && !empty($project) && count($project) > 0) {

                return $project[0];
            }else{

                return null;
            }

        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch class from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function getBillByCompanyId($companyId)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $bills = $this->dataService->Query("SELECT * FROM Bill MAXRESULTS 1000");

        if (isset($bills) && !empty($bills) && count($bills) > 0) {

            return $bills;
        }else{

            return null;
        }
    }

    public function getInvoiceByCompanyId(int $companyId)
    {
        try {

            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $invoices = $this->dataService->Query("SELECT * FROM Invoice MAXRESULTS 1000");

            if (isset($invoices) && !empty($invoices) && count($invoices) > 0) {

                return $invoices;
            }else{

                return null;
            }

        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch invoices from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function getSaleReceiptByCompanyId(int $companyId)
    {
        try {

            $this->getAccessToken($companyId);

            $this->dataService->throwExceptionOnError(true);

            $invoices = $this->dataService->Query("SELECT * FROM SalesReceipt MAXRESULTS 1000");

            if (isset($invoices) && !empty($invoices) && count($invoices) > 0) {

                return $invoices;
            }else{

                return null;
            }

        } catch (\Exception $e) {

            // Log the error message
            Log::error("Failed to fetch sale receipts from QuickBooks: " . $e->getMessage());

            // Optionally, rethrow the exception if you want to handle it further up the stack
            throw $e;
        }
    }

    public function storeClass(int $companyId, string $name)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        $theResourceObj = QuickBookClass::create(["Name" => $name]);

        try {

            $resultingObj = $this->dataService->Add($theResourceObj);

            return $resultingObj;
            
        } catch (ServiceException $ex) {
            
            throw new Exception($ex->getMessage());
        }
    }

    public function storeCreditMemo($companyId, $creditMemoData)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);


        if($creditMemoData['isGstEnable']){

            $createCreditMemo = [
                "CustomerRef" => [
                    "value" => $creditMemoData['customerId']
                ],
                "PrivateNote" => $creditMemoData['name'],
                "TxnDate" => $creditMemoData['invoiceDate'],
                "Line" => [
                    [
                        "Amount" => $creditMemoData['netAmountTaxable'],
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => [
                            "ItemRef" => [
                                "value" => $creditMemoData['itemRef']
                            ],
                            "ClassRef" => [
                                "value" => $creditMemoData['classRef']
                            ],
                            "TaxCodeRef" => [
                                'value' => $creditMemoData['taxCodeRef'],
                            ],
                            "TaxInclusiveAmt" => $creditMemoData['taxInclusiveAmt'],
                        ]
                    ]
                ],
                "TxnTaxDetail" => [
                    "TotalTax" => $creditMemoData['totalTax'], // Assuming 'totalTax' is part of $invoiceData
                    "TaxLine" => [
                        "Amount" => $creditMemoData['totalTax'],
                        "DetailType" => "TaxLineDetail",
                        "TaxLineDetail" => [
                            "TaxRateRef" => $creditMemoData['taxRateRef'],
                            "PercentBased" => "true",
                            "TaxPercent" => $creditMemoData['TaxPercent'],
                            "NetAmountTaxable" => $creditMemoData['netAmountTaxable'],
                        ]
                    ]

                ],
                "CustomerMemo" => [
                    "value" => $creditMemoData['remark']
                ]
            ];
            
        }else{
             $createCreditMemo = [
                "CustomerRef" => [
                    "value" => $creditMemoData['customerId']
                ],
                "PrivateNote" => $creditMemoData['name'],
                "TxnDate" => $creditMemoData['invoiceDate'],
                "Line" => [
                    [
                        "Amount" => $creditMemoData['netAmountTaxable'],
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => [
                            "ItemRef" => [
                                "value" => $creditMemoData['itemRef']
                            ],
                            "ClassRef" => [
                                "value" => $creditMemoData['classRef']
                            ]
                        ]
                    ]
                ],
                "CustomerMemo" => [
                    "value" => $creditMemoData['remark']
                ]
            ];
        }

        Log::info('Create Credit Note Data Befor Send To QBO:', $createCreditMemo);

        $theResourceObj = CreditMemo::create($createCreditMemo);

        try {
            $resultingObj = $this->dataService->Add($theResourceObj);
            return $resultingObj;
        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function saveCreditNotePdf($companyId,$invoiceId)
    {
        $this->getAccessToken($companyId);

        $this->dataService->throwExceptionOnError(true);

        try {

            // Find the Credit Memo by ID
            $invoice = $this->dataService->FindbyId('creditmemo', $invoiceId);

            $filePath = storage_path('app/public/QBO');

            if (!Storage::exists('public/QBO')) {
                Storage::makeDirectory('public/QBO');
            }

            $directoryForThePDF = $this->dataService->DownloadPDF($invoice, $filePath);

            $result = preg_replace('/^.*QBO\//', '', $directoryForThePDF);

            return $result;

        } catch (ServiceException $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}