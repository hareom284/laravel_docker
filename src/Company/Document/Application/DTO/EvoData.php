<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Src\Company\Document\Infrastructure\EloquentModels\EvoEloquentModel;

class EvoData
{
    public function __construct(
        public readonly ?int $id,
        // public readonly string $version_number,
        public readonly float $total_amount,
        public readonly float $grand_total,
        // public readonly string $signed_date,
        public readonly ?string $additional_notes,
        public readonly int $project_id,
        public readonly ?int $signed_by_salesperson_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $evo_id = null): EvoData
    {
        return new self(
            id: $evo_id,
            // version_number: $request->string('version_number'),
            total_amount: $request->float('total_amount'),
            grand_total: $request->float('grand_total'),
            // signed_date: $request->string('signed_date'),
            additional_notes: $request->string('additional_notes'),
            project_id: $request->integer('project_id'),
            signed_by_salesperson_id: $request->integer('signed_by_salesperson_id')
        );
    }

    public static function fromEloquent(EvoEloquentModel $evoEloquent): self
    {
        return new self(
            id: $evoEloquent->id,
            // version_number: $evoEloquent->version_number,
            total_amount: $evoEloquent->total_amount,
            grand_total: $evoEloquent->grand_total,
            // signed_date: $evoEloquent->signed_date,
            additional_notes: $evoEloquent->additional_notes,
            project_id: $evoEloquent->project_id,
            signed_by_salesperson_id: $evoEloquent->signed_by_salesperson_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            // 'version_number' => $this->version_number,
            'total_amount' => $this->total_amount,
            'grand_total' => $this->grand_total,
            // 'signed_date' => $this->signed_date,
            'additional_notes' => $this->additional_notes,
            'project_id' => $this->project_id,
            'signed_by_salesperson_id' => $this->signed_by_salesperson_id
        ];
    }
}