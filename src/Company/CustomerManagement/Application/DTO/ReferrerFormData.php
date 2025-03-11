<?php

namespace Src\Company\CustomerManagement\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\ReferrerFormEloquentModel;

class ReferrerFormData
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

    public static function fromEloquent(ReferrerFormEloquentModel $referrerFormEloquent): self
    {

        return new self(
            id: $referrerFormEloquent->id,
            owner_id: $referrerFormEloquent->owner_id,
            referrer_id: $referrerFormEloquent->referrer_id,
            referrer_properties: $referrerFormEloquent->referrer_properties,
            signed_by_salesperson_id: $referrerFormEloquent->signed_by_salesperson_id,
            signed_by_management_id: $referrerFormEloquent->signed_by_management_id,
            owner_signature: $referrerFormEloquent->owner_signature,
            salesperson_signature: $referrerFormEloquent->salesperson_signature,
            management_signature: $referrerFormEloquent->management_signature,
            date_of_referral: $referrerFormEloquent->date_of_referral,
            relation_with_referrer: $referrerFormEloquent->relation_with_referrer
        );
    }

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
