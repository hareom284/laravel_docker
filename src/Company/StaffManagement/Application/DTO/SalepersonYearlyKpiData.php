<?php

namespace Src\Company\StaffManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonYearlyKpiEloquentModel;

class SalepersonYearlyKpiData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $year,
        public readonly ?string $management_target,
        public readonly ?int $saleperson_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): SalepersonYearlyKpiData
    {
        return new self(
            id: $id,
            year: $request->string('year'),
            management_target: $request->string('management_target'),
            saleperson_id: $request->integer('saleperson_id'),
        );
    }

    public static function fromEloquent(SalepersonYearlyKpiEloquentModel $kpiRecordEloquent): self
    {
        return new self(
            id: $kpiRecordEloquent->id,
            year: $kpiRecordEloquent->year,
            management_target: $kpiRecordEloquent->management_target,
            saleperson_id: $kpiRecordEloquent->saleperson_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'management_target' => $this->management_target,
            'saleperson_id' => $this->saleperson_id,
        ];
    }
}