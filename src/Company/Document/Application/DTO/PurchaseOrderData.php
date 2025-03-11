<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;

class PurchaseOrderData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly int $vendor_id,
        public readonly string $date,
        public readonly ?string $time,
        public readonly ?string $pages,
        public readonly ?string $sales_rep_signature,
        public readonly ?string $remark,
        public readonly ?string $delivery_date,
        public readonly ?string $delivery_time_of_the_day,
        public readonly ?int $purchase_order_number,
    ) {
    }

    public static function fromRequest(Request $request, ?int $po_id = null): PurchaseOrderData
    {
        return new self(
            id: $po_id,
            project_id: $request->integer('project_id'),
            vendor_id: $request->integer('vendor_id'),
            date: $request->string('date'),
            time: $request->string('time'),
            pages: $request->string('pages'),
            sales_rep_signature: $request->string('sales_rep_signature'),
            remark: $request->string('remark'),
            delivery_date: $request->string('delivery_date'),
            delivery_time_of_the_day: $request->string('delivery_time_of_the_day'),
            purchase_order_number: $request->integer('purchase_order_number')
        );
    }

    public static function fromEloquent(PurchaseOrderEloquentModel $poEloquent): self
    {
        return new self(
            id: $poEloquent->id,
            project_id: $poEloquent->project_id,
            vendor_id: $poEloquent->vendor_id,
            date: $poEloquent->date,
            time: $poEloquent->time,
            pages: $poEloquent->pages,
            sales_rep_signature: $poEloquent->sales_rep_signature,
            remark: $poEloquent->remark,
            delivery_date: $poEloquent->delivery_date,
            delivery_time_of_the_day: $poEloquent->delivery_time_of_the_day,
            purchase_order_number: $poEloquent->purchase_order_number,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'vendor_id' => $this->vendor_id,
            'date' => $this->date,
            'time' => $this->time,
            'pages' => $this->pages,
            'sales_rep_signature' => $this->sales_rep_signature,
            'remark' => $this->remark,
            'delivery_date' => $this->delivery_date,
            'delivery_time_of_the_day' => $this->delivery_time_of_the_day,
            'purchase_order_number' => $this->purchase_order_number
        ];
    }
}
