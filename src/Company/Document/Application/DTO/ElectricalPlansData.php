<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\ElectricalPlansEloquentModel;

class ElectricalPlansData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $date_uploaded,
        public readonly string $document_file,
        public readonly ?string $customer_signature,
        public readonly int $project_id
    )
    {}

    public static function fromRequest(Request $request, ?int $electrical_plan_id = null): ElectricalPlansData
    {
        return new self(
            id: $electrical_plan_id,
            date_uploaded: $request->string('date_uploaded'),
            document_file: $request->string('document_file'),
            customer_signature: $request->string('customer_signature'),
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(ElectricalPlansEloquentModel $electricalPlansEloquent): self
    {
        return new self(
            id: $electricalPlansEloquent->id,
            date_uploaded: $electricalPlansEloquent->date_uploaded,
            document_file: $electricalPlansEloquent->document_file,
            customer_signature: $electricalPlansEloquent->customer_signature,
            project_id: $electricalPlansEloquent->project_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date_uploaded' => $this->date_uploaded,
            'document_file' => $this->document_file,
            'customer_signature' => $this->customer_signature,
            'project_id' => $this->project_id
        ];
    }
}