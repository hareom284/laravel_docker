<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class PurchaseOrderTemplateItem extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly ?string $code,
        public readonly ?string $quantity,
        public readonly ?string $size,
        public readonly int $vendor_category_id,
        public readonly int $company_id
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'code' => $this->code,
            'quantity' => $this->quantity,
            'size' => $this->size,
            'vendor_category_id' => $this->vendor_category_id,
            'company_id' => $this->company_id
        ];
    }
}
