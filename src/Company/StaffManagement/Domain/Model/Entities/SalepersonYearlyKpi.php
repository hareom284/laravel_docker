<?php

namespace Src\Company\StaffManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class SalepersonYearlyKpi extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $year,
        public readonly ?string $management_target,
        public readonly ?int $saleperson_id,
    ) {}



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
