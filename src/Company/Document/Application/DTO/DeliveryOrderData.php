<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\DeliveryOrderEloquentModel;

class DeliveryOrderData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly ?string $do_no,
        public readonly ?string $po_no,
        public readonly ?string $quotation_no,
        public readonly ?string $date,
    )
    {}

    public static function fromRequest(Request $request, ?int $design_work_id = null): DeliveryOrderData
    {
        return new self(
            id: $design_work_id,
            project_id: $request->integer('project_id'),
            do_no: $request->string('do_no'),
            po_no: $request->string('po_no'),
            quotation_no: $request->string('quotation_no'),
            date: $request->string('date')
        );
    }

    public static function fromEloquent(DeliveryOrderEloquentModel $deliveryOrderEloquent): self
    {
        return new self(
            id: $deliveryOrderEloquent->id,
            project_id: $deliveryOrderEloquent->project_id,
            do_no: $deliveryOrderEloquent->do_no,
            po_no: $deliveryOrderEloquent->po_no,
            quotation_no: $deliveryOrderEloquent->quotation_no,
            date: $deliveryOrderEloquent->date,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'do_no' => $this->do_no,
            'po_no' => $this->po_no,
            'quotation_no' => $this->quotation_no,
            'date' => $this->date
        ];
    }
}