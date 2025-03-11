<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;

class VendorData
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
        public readonly ?int $vendor_category_id
    )
    {}

    public static function fromRequest(Request $request, ?int $vendor_id = null): VendorData
    {
        return new self(
            id: $vendor_id,
            vendor_name: $request->string('vendor_name'),
            contact_person: $request->string('contact_person'),
            contact_person_number: $request->integer('contact_person_number'),
            email: $request->string('email'),
            street_name: $request->string('street_name'),
            block_num: $request->integer('block_num'),
            unit_num: $request->string('unit_num'),
            postal_code: $request->integer('postal_code'),
            fax_number: $request->integer('fax_number'),
            rebate: $request->float('rebate'),
            vendor_category_id: $request->integer('vendor_category_id'),
        );
    }

    public static function fromEloquent(VendorEloquentModel $vendor): self
    {
        return new self(
            id: $vendor->id,
            vendor_name: $vendor->vendor_name,
            contact_person: $vendor->contact_person,
            contact_person_number: $vendor->contact_person_number,
            email: $vendor->email,
            street_name: $vendor->street_name,
            block_num: $vendor->block_num,
            unit_num: $vendor->unit_num,
            postal_code: $vendor->postal_code,
            fax_number: $vendor->fax_number,
            rebate: $vendor->rebate,
            vendor_category_id: $vendor->vendor_category_id,
        );
    }

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
        ];
    }
}