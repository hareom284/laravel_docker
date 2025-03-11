<?php

namespace Src\Company\System\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\System\Infrastructure\EloquentModels\KpiRecordEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;

class CompanyKpiData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $type,
        public readonly ?string $period,
        public readonly ?string $target,
        public readonly ?int $company_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $id = null): CompanyKpiData
    {
        return new self(
            id: $id,
            type: $request->string('type'),
            period: $request->string('period'),
            target: $request->string('target'),
            company_id: $request->integer('company_id'),
        );
    }

    public static function fromEloquent(KpiRecordEloquentModel $kpiRecordEloquent): self
    {
        return new self(
            id: $kpiRecordEloquent->id,
            type: $kpiRecordEloquent->type,
            period: $kpiRecordEloquent->period,
            target: $kpiRecordEloquent->target,
            company_id: $kpiRecordEloquent->company_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'period' => $this->period,
            'target' => $this->target,
            'company_id' => $this->company_id,
        ];
    }
}