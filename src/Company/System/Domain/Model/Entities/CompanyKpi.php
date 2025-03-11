<?php

namespace Src\Company\System\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class CompanyKpi extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $type,
        public readonly ?string $period,
        public readonly ?string $target,
        public readonly ?int $company_id,
    ) {}



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
