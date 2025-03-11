<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Src\Company\CompanyManagement\Domain\Repositories\AccountingServiceInterface;
use Src\Company\CompanyManagement\Domain\Services\QuickbookService;
use Src\Company\Project\Application\DTO\SupplierCostingPaymentData;
use Src\Company\Project\Application\Mappers\SupplierCostingPaymentMapper;
use Src\Company\Project\Domain\Model\Entities\SupplierCostingPayment;
use Src\Company\Project\Domain\Repositories\SupplierCostingPaymentRepositoryInterface;
use Src\Company\Project\Domain\Resources\SupplierCostingPaymentDetailResource;
use Src\Company\Project\Domain\Resources\SupplierCostingPaymentListResource;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingPaymentEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class SupplierCostingPaymentRepository implements SupplierCostingPaymentRepositoryInterface
{
    private $accountingService;

    public function __construct(AccountingServiceInterface $accountingService = null)
    {
        $this->accountingService = $accountingService;
    }

    public function index(array $filters)
    {
        $perPage = $filters['perPage'] ?? 10;

        $query = SupplierCostingPaymentEloquentModel::query();

        if (isset($filters['vendorId'])) {

            $vendorId = $filters['vendorId'];
        
            // Filter the SupplierCostingPaymentEloquentModel based on vendorId
            $query->whereHas('supplierCostings', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            });
        }

        if(isset($filters['projectId'])){

            $projectId = $filters['projectId'];

            // Filter the SupplierCostingPaymentEloquentModel based on projectId
            $query->whereHas('supplierCostings', function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            });
        }

        $data = $query->orderBy('status','ASC')->paginate($perPage);

        $supplierCostingPayments = SupplierCostingPaymentListResource::collection($data);

        $links = [
            'first' => $supplierCostingPayments->url(1),
            'last' => $supplierCostingPayments->url($supplierCostingPayments->lastPage()),
            'prev' => $supplierCostingPayments->previousPageUrl(),
            'next' => $supplierCostingPayments->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $supplierCostingPayments->currentPage(),
            'from' => $supplierCostingPayments->firstItem(),
            'last_page' => $supplierCostingPayments->lastPage(),
            'path' => $supplierCostingPayments->url($supplierCostingPayments->currentPage()),
            'per_page' => $perPage,
            'to' => $supplierCostingPayments->lastItem(),
            'total' => $supplierCostingPayments->total(),
        ];
        $responseData['data'] = $supplierCostingPayments;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function getPendingApprovalSupplierCostingPayment()
    {
        $data = SupplierCostingPaymentEloquentModel::query()->with('accountant','supplierCosting.purchseOrder','supplierCosting.project.customer','supplierCosting.project.property')->orderBy('status','ASC')->get();

        return SupplierCostingPaymentListResource::collection($data);

    }

    public function SupplierCostingPaymentDetail(int $id)
    {
        $data = SupplierCostingPaymentEloquentModel::query()->with('accountant','supplierCostings')->where('id',$id)->first();

        return new SupplierCostingPaymentDetailResource($data);

    }

    public function store(SupplierCostingPayment $supplierCostingPayment,array $vendorInvoiceIds): SupplierCostingPaymentData
    {
        return DB::transaction(function () use ($supplierCostingPayment,$vendorInvoiceIds) {

            $supplierCostingPaymentEloquent = SupplierCostingPaymentMapper::toEloquent($supplierCostingPayment);

            $supplierCostingPaymentEloquent->save();

            $supplierCostingPaymentEloquent->supplierCostings()->sync($vendorInvoiceIds);
            
            return SupplierCostingPaymentData::fromEloquent($supplierCostingPaymentEloquent);
        });
    }

    public function managerSign($request)
    {
        // Manager Signature
        $managerSignature =  time().'_manager.'.$request->file('managerSign')->extension();

        $managerSignaturePath = 'supplier_costing/' . $managerSignature;
    
        Storage::disk('public')->put($managerSignaturePath, file_get_contents($request->file('managerSign')));

        $managerSignatureFile = $managerSignature;

        $user = auth('sanctum')->user();

        $supplierCostingPaymentEloquent = SupplierCostingPaymentEloquentModel::query()->findOrFail($request->id);

        $supplierCostingPaymentEloquent->manager_signature = $managerSignatureFile;

        $supplierCostingPaymentEloquent->signed_by_manager_id = $user->id;

        $supplierCostingPaymentEloquent->status = 1;

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', 'accounting_software_integration')->first();

        /*
        if($generalSettingEloquent && $generalSettingEloquent->value != 'none'){

            $amount = $supplierCostingPaymentEloquent->amount;
            $remark = $supplierCostingPaymentEloquent->remark;
            $costings = $supplierCostingPaymentEloquent->supplierCostings;
            $vendorId = $costings[0]->vendor->quick_book_vendor_id;
            $invoiceId = [];

            foreach ($costings as $costing) {
                $txn['TxnId'] = $costing->quick_book_bill_id;
                $txn['TxnType'] = "Bill";

                array_push($invoiceId, $txn);
                
                $companyId = $costing->project->company_id;
            }

            $data = [
                "VendorRef" => [
                    "value" => $vendorId,
                ],
                "PayType" => "Cash",
                "CheckPayment" => [
                    "BankAccountRef" => [
                      "value" => $request->bank_info_id,
                    ]
                ],
                "TotalAmt" => $amount,
                "PrivateNote" => $remark,
                "Line" => [
                    [
                        "Amount" => $amount,
                        "LinkedTxn" => $invoiceId
                    ]
                ],
                
            ];

            $quickBookBillPayment = $this->accountingService->storeBillPyament($data);

            $supplierCostingPaymentEloquent->quick_book_bill_payment_id = $quickBookBillPayment->Id;
        }
        */

        $supplierCostingPaymentEloquent->save();

        return $supplierCostingPaymentEloquent;
    }

}