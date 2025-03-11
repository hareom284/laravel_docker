<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\DeliveryOrder;
use Src\Company\Document\Infrastructure\EloquentModels\DeliveryOrderEloquentModel;
use Illuminate\Support\Facades\Storage;


class DeliveryOrderMapper
{
    public static function fromRequest(Request $request, ?int $delivery_order_id = null): DeliveryOrder
    {

        return new DeliveryOrder(
            id: $delivery_order_id,
            project_id: $request->project_id,
            do_no: $request->do_no,
            po_no: $request->po_no,
            quotation_no: $request->quotation_no,
            date: $request->date,

        );
    }

    public static function fromEloquent(DeliveryOrderEloquentModel $deliveryOrderEloquent): DeliveryOrder
    {
        return new DeliveryOrder(
            id: $deliveryOrderEloquent->id,
            project_id: $deliveryOrderEloquent->project_id,
            do_no: $deliveryOrderEloquent->do_no,
            po_no: $deliveryOrderEloquent->po_no,
            quotation_no: $deliveryOrderEloquent->quotation_no,
            date: $deliveryOrderEloquent->date,

        );
    }

    public static function toEloquent(DeliveryOrder $deliveryOrder): DeliveryOrderEloquentModel
    {
        $currentDate = Carbon::now();

        $deliveryOrderEloquent = new DeliveryOrderEloquentModel();
        if ($deliveryOrder->id) {
            $deliveryOrderEloquent = DeliveryOrderEloquentModel::query()->findOrFail($deliveryOrder->id);
        }
        $deliveryOrderEloquent->project_id = $deliveryOrder->project_id;
        $deliveryOrderEloquent->do_no = $deliveryOrder->do_no;
        $deliveryOrderEloquent->po_no = $deliveryOrder->po_no;
        $deliveryOrderEloquent->quotation_no = $deliveryOrder->quotation_no;
        $deliveryOrderEloquent->date = $currentDate;

        return $deliveryOrderEloquent;
    }
}