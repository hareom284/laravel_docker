<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Vendor extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $vendor_name,
        public readonly ?string $contact_person,
        public readonly ?int $contact_person_number,
        public readonly ?string $email,
        public readonly ?string $street_name,
        public readonly ?string $block_num,
        public readonly ?string $unit_num,
        public readonly ?int $postal_code,
        public readonly ?int $fax_number,
        public readonly ?float $rebate,
        public readonly ?int $vendor_category_id,
        public readonly ?int $quick_book_vendor_id,
        public readonly ?int $user_id,
        public readonly ?string $name_prefix,
        public readonly ?string $contact_person_last_name,
        public readonly ?string $prefix,
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'vendor_name' => $this->vendor_name,
            'contact_person' => $this->contact_person,
            'contact_person_number' => $this->contact_person_number,
            'email' => $this->email,
            'street_name' => $this->street_name,
            'block_num' => $this->block_num,
            'unit_num' => $this->unit_num,
            'postal_code' => $this->postal_code,
            'fax_number' => $this->fax_number,
            'rebate' => $this->rebate,
            'vendor_category_id' => $this->vendor_category_id,
            'quick_book_vendor_id' => $this->quick_book_vendor_id,
            'user_id' => $this->user_id,
            'name_prefix' => $this->name_prefix,
            'contact_person_last_name' => $this->contact_person_last_name,
            'prefix' => $this->prefix,
        ];
    }
}
