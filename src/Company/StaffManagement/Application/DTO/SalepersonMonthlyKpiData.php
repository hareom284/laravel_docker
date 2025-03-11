<?php

namespace Src\Company\StaffManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonMonthlyKpiEloquentModel;

class SalepersonMonthlyKpiData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $year,
        public readonly ?string $month,
        public readonly ?string $target,
        public readonly ?int $saleperson_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): SalepersonMonthlyKpiData
    {
        return new self(
            id: $id,
            year: $request->string('year'),
            month: $request->string('month'),
            target: $request->string('target'),
            saleperson_id: $request->integer('saleperson_id'),
        );
    }

    public static function fromEloquent(SalepersonMonthlyKpiEloquentModel $kpiRecordEloquent): self
    {
        return new self(
            id: $kpiRecordEloquent->id,
            year: $kpiRecordEloquent->year,
            month: $kpiRecordEloquent->month,
            target: $kpiRecordEloquent->target,
            saleperson_id: $kpiRecordEloquent->saleperson_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'month' => $this->month,
            'target' => $this->target,
            'saleperson_id' => $this->saleperson_id,
        ];
    }
}