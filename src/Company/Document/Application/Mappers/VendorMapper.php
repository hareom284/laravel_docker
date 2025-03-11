<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\Vendor;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;

class VendorMapper
{
    public static function fromRequest(Request $request, ?int $vendor_id = null): Vendor
    {
        return new Vendor(
            id: $vendor_id,
            vendor_name: $request->string('vendor_name'),
            contact_person: $request->filled('contact_person') ? $request->string('contact_person') : null,
            contact_person_number: $request->filled('contact_person_number') ? $request->integer('contact_person_number') : null,
            email: $request->filled('email') ? $request->string('email') : null,
            street_name: $request->filled('street_name') ? $request->string('street_name') : null,
            block_num: $request->filled('block_num') ? $request->string('block_num') : null,
            unit_num: $request->filled('unit_num') ? $request->string('unit_num') : null,
            postal_code: $request->filled('postal_code') ? $request->integer('postal_code') : null,
            fax_number: $request->filled('postal_code') ? $request->integer('fax_number') : null,
            rebate: $request->filled('rebate') ? $request->float('rebate') : null,
            vendor_category_id: $request->filled('vendor_category_id') ? $request->integer('vendor_category_id') : null,
            quick_book_vendor_id: null,
            user_id : $request->filled('user_id') ? $request->integer('user_id') : null,
            name_prefix: $request->filled('name_prefix') ? $request->string('name_prefix') : null,
            contact_person_last_name: $request->filled('contact_person_last_name') ? $request->string('contact_person_last_name') : null,
            prefix: $request->filled('prefix') ? $request->string('prefix') : null,
        );
    }

    public static function fromEloquent(VendorEloquentModel $vendorEloquent): Vendor
    {
        return new Vendor(
            id: $vendorEloquent->id,
            vendor_name: $vendorEloquent->vendor_name,
            contact_person: $vendorEloquent->contact_person,
            contact_person_number: $vendorEloquent->contact_person_number,
            email: $vendorEloquent->email,
            street_name: $vendorEloquent->street_name,
            block_num: $vendorEloquent->block_num,
            unit_num: $vendorEloquent->unit_num,
            postal_code: $vendorEloquent->postal_code,
            fax_number: $vendorEloquent->fax_number,
            rebate: $vendorEloquent->rebate,
            vendor_category_id: $vendorEloquent->vendor_category_id,
            quick_book_vendor_id: $vendorEloquent->quick_book_vendor_id,
            user_id : $vendorEloquent->user_id,
            name_prefix : $vendorEloquent->name_prefix,
            contact_person_last_name : $vendorEloquent->contact_person_last_name,
            prefix : $vendorEloquent->prefix
        );
    }

    public static function toEloquent(Vendor $vendor): VendorEloquentModel
    {
        $vendorEloquent = new VendorEloquentModel();
        if ($vendor->id) {
            $vendorEloquent = VendorEloquentModel::query()->findOrFail($vendor->id);
        }
        $vendorEloquent->vendor_name = $vendor->vendor_name;
        $vendorEloquent->contact_person = $vendor->contact_person;
        $vendorEloquent->contact_person_number = $vendor->contact_person_number;
        $vendorEloquent->email = $vendor->email;
        $vendorEloquent->street_name = $vendor->street_name;
        $vendorEloquent->block_num = $vendor->block_num;
        $vendorEloquent->unit_num = $vendor->unit_num;
        $vendorEloquent->postal_code = $vendor->postal_code;
        $vendorEloquent->fax_number = $vendor->fax_number;
        $vendorEloquent->rebate = $vendor->rebate;
        $vendorEloquent->vendor_category_id = $vendor->vendor_category_id;
        $vendorEloquent->quick_book_vendor_id = $vendorEloquent->quick_book_vendor_id;
        $vendorEloquent->user_id = $vendor->user_id;
        $vendorEloquent->name_prefix = $vendor->name_prefix;
        $vendorEloquent->contact_person_last_name = $vendor->contact_person_last_name;
        $vendorEloquent->prefix = $vendor->prefix;
        return $vendorEloquent;
    }
}
