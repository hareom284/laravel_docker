<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateEloquentModel;

class HandoverCertificateData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $project_id,
        public readonly ?int $signed_by_manager_id,
        public readonly string $date,
        public readonly ?string $last_edited,
        public readonly ?string $customer_signature,
        public readonly string $salesperson_signature,
        public readonly ?int $signed_by_salesperson_id,
        public readonly ?string $manager_signature,
        public readonly int $status,
    ) {
    }

    public static function fromRequest(Request $request, ?int $id = null): HandoverCertificateData
    {
        return new self(
            id: $id,
            project_id: $request->integer('project_id'),
            signed_by_manager_id: $request->integer('signed_by_manager_id'),
            date: $request->string('date'),
            last_edited: $request->string('last_edited'),
            customer_signature: $request->string('customer_signature'),
            salesperson_signature: $request->string('salesperson_signature'),
            manager_signature: $request->string('manager_signature'),
            status: $request->integer('status'),
        );
    }

    public static function fromEloquent(HandoverCertificateEloquentModel $handoverEloquent): self
    {
        return new self(
            id: $handoverEloquent->id,
            project_id: $handoverEloquent->project_id,
            signed_by_manager_id: $handoverEloquent->signed_by_manager_id,
            date: $handoverEloquent->date,
            last_edited: $handoverEloquent->last_edited,
            customer_signature: $handoverEloquent->customer_signature,
            salesperson_signature: $handoverEloquent->salesperson_signature,
            signed_by_salesperson_id: $handoverEloquent->signed_by_salesperson_id,
            manager_signature: $handoverEloquent->manager_signature,
            status: $handoverEloquent->status,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'signed_by_manager_id' => $this->signed_by_manager_id,
            'date' => $this->date,
            'last_edited' => $this->last_edited,
            'customer_signature' => $this->customer_signature,
            'salesperson_signature' => $this->salesperson_signature,
            'signed_by_salesperson_id' => $this->signed_by_salesperson_id,
            'manager_signature' => $this->manager_signature,
            'status' => $this->status,
        ];
    }
}
