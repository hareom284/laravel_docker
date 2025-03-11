<?php

namespace Src\Company\CustomerManagement\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class ReferrerForm extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $owner_id,
        public readonly ?int $referrer_id,
        public readonly ?string $referrer_properties,
        public readonly ?int $signed_by_salesperson_id,
        public readonly ?int $signed_by_management_id,
        public readonly ?string $owner_signature,
        public readonly ?string $salesperson_signature,
        public readonly ?string $management_signature,
        public readonly ?string $date_of_referral,
        public readonly ?string $relation_with_referrer
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'referrer_id' => $this->referrer_id,
            'referrer_properties' => $this->referrer_properties,
            'signed_by_salesperson_id' => $this->signed_by_salesperson_id,
            'signed_by_management_id' => $this->signed_by_management_id,
            'owner_signature' => $this->owner_signature,
            'salesperson_signature' => $this->salesperson_signature,
            'management_signature' => $this->management_signature,
            'date_of_referral' => $this->date_of_referral,
            'relation_with_referrer' => $this->relation_with_referrer
        ];
    }
}
