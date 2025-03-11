<?php

namespace Src\Company\StaffManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class SalepersonMonthlyKpi extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $year,
        public readonly ?string $month,
        public readonly ?string $target,
        public readonly ?int $saleperson_id,
    ) {}



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
