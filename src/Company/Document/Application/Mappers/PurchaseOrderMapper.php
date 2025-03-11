<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Illuminate\Support\Facades\Storage;


class PurchaseOrderMapper
{
    public static function fromRequest(Request $request, ?int $po_id = null): PurchaseOrder
    {
        $fileName =  time().'.'.$request->file('sales_rep_signature')->extension();

        $filePath = 'po/' . $fileName;
    
        Storage::disk('public')->put($filePath, file_get_contents($request->file('sales_rep_signature')));

        $documentFile = $fileName;


        return new PurchaseOrder(
            id: $po_id,
            project_id: $request->integer('project_id'),
            vendor_id: $request->integer('vendor_id'),
            date: $request->string('date'),
            time: $request->filled('time') ? $request->string('time') : null,
            pages: $request->filled('pages') ? $request->string('pages') : null,
            attn: $request->string('attn'),
            sales_rep_signature: $documentFile,
            remark: $request->string('remark'),
            delivery_date: $request->string('delivery_date'),
            delivery_time_of_the_day: $request->string('delivery_time_of_the_day'),
            purchase_order_number: $request->integer('purchase_order_number')
        );
    }

    public static function fromEloquent(PurchaseOrderEloquentModel $poEloquent): PurchaseOrder
    {
        return new PurchaseOrder(
            id: $poEloquent->id,
            project_id: $poEloquent->project_id,
            vendor_id: $poEloquent->vendor_id,
            date: $poEloquent->date,
            attn: $poEloquent->attn,
            time: $poEloquent->time,
            pages: $poEloquent->pages,
            sales_rep_signature: $poEloquent->sales_rep_signature,
            remark: $poEloquent->remark,
            delivery_date: $poEloquent->delivery_date,
            delivery_time_of_the_day: $poEloquent->delivery_time_of_the_day,
            purchase_order_number: $poEloquent->purchase_order_number
        );
    }

    public static function toEloquent(PurchaseOrder $po): PurchaseOrderEloquentModel
    {
        $poEloquent = new PurchaseOrderEloquentModel();
        if ($po->id) {
            $poEloquent = PurchaseOrderEloquentModel::query()->findOrFail($po->id);
        }

        $salePersonId = auth('sanctum')->user()->id;

        $poEloquent->project_id = $po->project_id;
        $poEloquent->vendor_id = $po->vendor_id;
        $poEloquent->date = $po->date;
        $poEloquent->time = $po->time;
        $poEloquent->pages = $po->pages;
        $poEloquent->attn = $po->attn;
        $poEloquent->sales_rep_signature = $po->sales_rep_signature;
        $poEloquent->remark = $po->remark;
        $poEloquent->delivery_date = $po->delivery_date;
        $poEloquent->delivery_time_of_the_day = $po->delivery_time_of_the_day;
        $poEloquent->purchase_order_number = $po->purchase_order_number;
        $poEloquent->status = 2;
        $poEloquent->sale_rep_id = $salePersonId;
        return $poEloquent;
    }
}