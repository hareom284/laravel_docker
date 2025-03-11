<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use GuzzleHttp\Psr7\Request;
use Src\Company\Document\Application\DTO\PurchaseOrderData;
use Src\Company\Document\Application\Mappers\PurchaseOrderMapper;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;
use Src\Company\Document\Domain\Repositories\PurchaseOrderRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderItemEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Illuminate\Support\Facades\DB;
use Src\Company\Document\Domain\Resources\PurchaseOrderResource;
use Src\Company\Document\Domain\Resources\PurchaseOrderWithFilterResource;
use Src\Company\Document\Domain\Resources\PurchaseOrderByProjectResource;
use Src\Company\Document\Domain\Resources\PurchaseOrderWithItemResource;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Mail\PurchaseOrderNotiEmail;
use Illuminate\Support\Facades\Mail;
use Src\Company\Document\Domain\Resources\PurchaseOrderResourceForByStatus;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class PurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function getAllPurchaseOrders($filters = [])
    {
        $purchaseOrderEloquent = PurchaseOrderEloquentModel::all();

        $perPage = $filters['perPage'] ?? 999;

        $purchaseOrderEloquent = PurchaseOrderEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $purchaseOrders = PurchaseOrderWithFilterResource::collection($purchaseOrderEloquent);

        $links = [
            'first' => $purchaseOrders->url(1),
            'last' => $purchaseOrders->url($purchaseOrders->lastPage()),
            'prev' => $purchaseOrders->previousPageUrl(),
            'next' => $purchaseOrders->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $purchaseOrders->currentPage(),
            'from' => $purchaseOrders->firstItem(),
            'last_page' => $purchaseOrders->lastPage(),
            'path' => $purchaseOrders->url($purchaseOrders->currentPage()),
            'per_page' => $perPage,
            'to' => $purchaseOrders->lastItem(),
            'total' => $purchaseOrders->total(),
        ];
        $responseData['data'] = $purchaseOrders;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

	public function getByProjectId($projectId) //get for accountant
	{
		$purchaseOrderEloquent = PurchaseOrderEloquentModel::where('project_id',$projectId)->get();

        $purchaseOrders = PurchaseOrderByProjectResource::collection($purchaseOrderEloquent);

		return $purchaseOrders;
	}

    public function getPurchaseOrderByProjectId($projectId)
    {

        $poEloquent = PurchaseOrderEloquentModel::where('project_id',$projectId)->get();

        $pos = PurchaseOrderResource::collection($poEloquent);

        return $pos;
    }

    public function getPurchaseOrderByStatus($status)
    {
        $poEloquent = PurchaseOrderEloquentModel::query()->with('project','staff','staff.roles','project.customer','project.property','vendor_invoice')->where('status',$status)->get();

        $pos = PurchaseOrderResourceForByStatus::collection($poEloquent);

        return $pos;
    }

    public function getPurchaseOrderListByStatusOrder()
    {
        $poEloquent = PurchaseOrderEloquentModel::query()->with('project','staff','staff.roles','project.customer','project.property','vendor_invoice')->orderBy('status','ASC')->get();

        $pos = PurchaseOrderResourceForByStatus::collection($poEloquent);

        return $pos;
    }

    public function findById(int $id)
    {
        $poEloquent = PurchaseOrderEloquentModel::where('id',$id)->first();

        $data['po'] =  new PurchaseOrderWithItemResource($poEloquent);

        $data['poFooter'] = DocumentStandardEloquentModel::query()->where('company_id', $poEloquent->project->company->id)->where('name','purchase order')->first();

        return $data;
    }

    public function getPurchaseOrderNumberCount()
    {
        $purchaseOrderEloquent = PurchaseOrderEloquentModel::all();

        return $purchaseOrderEloquent->count();
    }

    public function findManagerEmails()
    {
        // Get emails of all users with the "manager" role
        $managerEmails = UserEloquentModel::whereHas('roles', function ($query) {
            $query->where('name', 'Management');
        })->pluck('email')->toArray();

        return $managerEmails;
    }

    public function sendEmails($emails,PurchaseOrderData $poData)
    {
        $project = ProjectEloquentModel::find($poData->project_id);

        $vendor = VendorEloquentModel::find($poData->vendor_id);

        $projectName = $project->property->street_name;

        $user = auth('sanctum')->user();

        $salespersonName = $user->first_name . " " . $user->last_name;

        $salespersonContact = $user->contact_no;

        $vendorName = $vendor->vendor_name;

        $poNumber = $poData->purchase_order_number;

        $requestDate= $poData->date;

        foreach($emails as $email)
        {
            $user = UserEloquentModel::where('email',$email)->first();

            $managerName = $user->first_name . " " . $user->last_name;

            Mail::to($email)->send(new PurchaseOrderNotiEmail($projectName,$salespersonName,$salespersonContact,$vendorName,$requestDate,$managerName,$poNumber));

        }

        return true;
    }

	public function store(PurchaseOrder $po): PurchaseOrderData
    {
        return DB::transaction(function () use ($po) {

            $poEloquent = PurchaseOrderMapper::toEloquent($po);

            $poEloquent->save();

            SupplierCostingEloquentModel::create([
                'status' => 0,
                'payment_amt' => 0,
                'gst_value' => 0,
                'discount_amt' => 0,
                'discount_percentage' => 0,
                'project_id' => $poEloquent->project_id,
                'vendor_id' => $poEloquent->vendor_id,
                'purchase_order_id' => $poEloquent->id,
            ]);

            return PurchaseOrderData::fromEloquent($poEloquent);
        });
    }

    public function updatePO($purchaseOrder,$id)
    {
        $poEloquent = PurchaseOrderEloquentModel::query()->findOrFail($id);

        if ($purchaseOrder->hasFile('sales_rep_signature')) {

            $picName =  time().'.'.$purchaseOrder->file('sales_rep_signature')->extension();

            $filePath = 'po/' . $picName;

            Storage::disk('public')->put($filePath, file_get_contents($purchaseOrder->file('sales_rep_signature')));

            $salesSignature = $picName;

        } else {

            $salesSignature = $poEloquent->sales_rep_signature;
        }

        $poEloquent->update([
            "project_id" => $purchaseOrder->project_id,
            "vendor_id" => $purchaseOrder->vendor_id,
            "date" => $purchaseOrder->date,
            "attn" => $purchaseOrder->attn,
            "time" => $purchaseOrder->time ?? null,
            "pages" => $purchaseOrder->pages ?? null,
            "remark" => $purchaseOrder->remark ?? "-",
            "delivery_date" => $purchaseOrder->delivery_date,
            "delivery_time_of_the_day" => $purchaseOrder->delivery_time_of_the_day,
            "purchase_order_number" => $purchaseOrder->purchase_order_number,
            "sales_rep_signature" => $salesSignature,
        ]);

        // Assuming $items contains the provided items data
        $items = json_decode($purchaseOrder->items, true);

        foreach ($items as $item) {
            if (isset($item['id']) && $item['id']) {
                // Update existing PoItems if the ID exists
                PurchaseOrderItemEloquentModel::where('id', $item['id'])->update([
                    'description' => $item['description'],
                    'code' => $item['code'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'],
                ]);
            } else {
                // Create new PoItems if ID does not exist
                PurchaseOrderItemEloquentModel::create([
                    'purchase_order_id' => $id,
                    'description' => $item['description'],
                    'code' => $item['code'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'],
                ]);
            }
        }

        return true;

    }

    //this update is for supplier costing salereport in accountant
    public function update($purchaseOrder,$id)
    {
        $poEloquent = PurchaseOrderEloquentModel::query()->findOrFail($id);

        $poEloquent->vendor_remark = $purchaseOrder->vendor_remark;

        $poEloquent->invoice_no = $purchaseOrder->invoice_no;

        $poEloquent->payment_amt = $purchaseOrder->payment_amt;

        $poEloquent->discount_percentage = $purchaseOrder->discount_percent;

        $poEloquent->discount_amt = $purchaseOrder->discount_amt;

        $poEloquent->credit_amt = $purchaseOrder->credit_amt;

        $fileName =  time().'.'.$purchaseOrder->file('document_file')->extension();

        $filePath = 'po/' . $fileName;

        Storage::disk('public')->put($filePath, file_get_contents($purchaseOrder->file('document_file')));

        $documentFile = $fileName;

        $poEloquent->document_file = $documentFile;

        $poEloquent->save();

        return $poEloquent;
    }

    public function managerSign($request)
    {
        // Manager Signature
        $managerSignature =  time().'_manager.'.$request->file('managerSign')->extension();

        $managerSignaturePath = 'po/' . $managerSignature;

        Storage::disk('public')->put($managerSignaturePath, file_get_contents($request->file('managerSign')));

        $managerSignatureFile = $managerSignature;

        $user = auth('sanctum')->user();

        $purchaseOrderEloquent = PurchaseOrderEloquentModel::query()->findOrFail($request->id);

        $purchaseOrderEloquent->manager_signature = $managerSignatureFile;

        $purchaseOrderEloquent->signed_by_manager_id = $user->id;

        $purchaseOrderEloquent->status = 3;

        $purchaseOrderEloquent->save();

        return $purchaseOrderEloquent;

    }

    public function delete(int $po_id): void
    {
        // Find the PurchaseOrderEloquentModel instance
        $poEloquent = PurchaseOrderEloquentModel::query()->findOrFail($po_id);

        // Delete the related PurchaseOrderItems
        $poEloquent->poItems()->delete();

        // Delete the PurchaseOrderEloquentModel instance
        $poEloquent->delete();
    }

    public function findQuotationItemsForPOQuery($projectId)
    {
        $quotationItems = generateContractItems($projectId);
        $transformedSections = collect($quotationItems)->map(function ($section) {
            return [
                'section_id' => $section['section_id'],
                'section_name' => $section['section_name'],
                'items' => collect($section['hasAOWData'])->flatMap(function ($aow) {
                    return $this->extractItems($aow['area_of_work_items']);
                })->values()->all(),
            ];
        });
        return $transformedSections;
    }

    function extractItems(array $items): array
    {
        $result = [];
    
        foreach ($items as $item) {
            // Add the current item
            $result[] = collect($item)->except('items')->toArray();
    
            // Recursively add nested items if they exist
            if (!empty($item['items'])) {
                $result = array_merge($result, $this->extractItems($item['items']));
            }
        }
    
        return $result;
    }

}
