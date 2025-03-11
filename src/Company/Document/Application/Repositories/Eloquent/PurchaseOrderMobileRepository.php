<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\DTO\PurchaseOrderData;
use Src\Company\Document\Application\Mappers\PurchaseOrderMapper;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;
use Src\Company\Document\Domain\Repositories\PurchaseOrderMobileRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Domain\Resources\PurchaseOrderMobileResource;
use Src\Company\Document\Domain\Resources\PurchaseOrderWithFilterResource;
use Src\Company\Document\Domain\Resources\PurchaseOrderWithItemMobileResource;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderItemEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;

class PurchaseOrderMobileRepository implements PurchaseOrderMobileRepositoryInterface
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

	public function getPurchaseOrderByProjectId($projectId)
    {
        $poEloquent = PurchaseOrderEloquentModel::where('project_id',$projectId)->get();

        $pos = PurchaseOrderMobileResource::collection($poEloquent);

        return $pos;
    }

    public function store(PurchaseOrder $po): PurchaseOrderData
    {
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

    public function findById(int $id)
    {
        $poEloquent = PurchaseOrderEloquentModel::where('id',$id)->first();

        $data['po'] =  new PurchaseOrderWithItemMobileResource($poEloquent);

        $data['poFooter'] = DocumentStandardEloquentModel::query()->where('company_id', $poEloquent->project->company->id)->where('name','purchase order')->first();

        return $data;
    }

    public function getPurchaseOrderNumberCount()
    {
        $purchaseOrderEloquent = PurchaseOrderEloquentModel::all();

        return $purchaseOrderEloquent->count();
    }

}
