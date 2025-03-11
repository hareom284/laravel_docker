<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\DesignWorkEloquentModel;

class DesignWorkData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $date,
        public readonly ?string $document_date,
        public readonly string $name,
        public readonly string $document_file,
        public readonly ?string $scale,
        public readonly ?string $request_status,
        public readonly ?string $last_edited,
        public readonly ?string $signed_date,
        public readonly int $designer_in_charge_id,
        public readonly int $project_id,
        public readonly ?int $drafter_in_charge_id
    )
    {}

    public static function fromRequest(Request $request, ?int $design_work_id = null): DesignWorkData
    {
        return new self(
            id: $design_work_id,
            date: $request->string('date'),
            document_date: $request->string('document_date'),
            name: $request->string('name'),
            document_file: $request->string('document_file'),
            scale: $request->string('scale'),
            request_status: $request->string('request_status'),
            last_edited: $request->string('last_edited'),
            signed_date: $request->string('signed_date'),
            designer_in_charge_id: $request->int('designer_in_charge_id'),
            project_id: $request->integer('project_id'),
            drafter_in_charge_id: $request->integer('drafter_in_charge_id'),
        );
    }

    public static function fromEloquent(DesignWorkEloquentModel $desginWorkEloquent): self
    {
        return new self(
            id: $desginWorkEloquent->id,
            date: $desginWorkEloquent->date,
            document_date: $desginWorkEloquent->document_date,
            name: $desginWorkEloquent->name,
            document_file: $desginWorkEloquent->document_file,
            scale: $desginWorkEloquent->scale,
            request_status: $desginWorkEloquent->request_status,
            last_edited: $desginWorkEloquent->last_edited,
            signed_date: $desginWorkEloquent->signed_date,
            designer_in_charge_id: $desginWorkEloquent->designer_in_charge_id,
            project_id: $desginWorkEloquent->project_id,
            drafter_in_charge_id: $desginWorkEloquent->drafter_in_charge_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'document_date' => $this->document_date,
            'name' => $this->name,
            'document_file' => $this->document_file,
            'scale' => $this->scale,
            'request_status' => $this->request_status,
            'last_edited' => $this->last_edited,
            'signed_date' => $this->signed_date,
            'designer_in_charge_id' => $this->designer_in_charge_id,
            'project_id' => $this->project_id,
            'drafter_in_charge_id' => $this->drafter_in_charge_id,
        ];
    }
}