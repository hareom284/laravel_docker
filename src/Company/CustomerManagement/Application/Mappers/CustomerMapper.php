<?php

namespace Src\Company\CustomerManagement\Application\Mappers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Src\Company\CustomerManagement\Domain\Model\Customer;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Illuminate\Support\Facades\Storage;

// use Src\Company\System\Domain\Model\User;
// use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class CustomerMapper
{
    public static function fromRequest(Request $request, ?int $customer_id = null, ?int $user_id = null): Customer
    {

        $managementId = null;

        if (auth('sanctum')->check()) {
            if(auth('sanctum')->user()->roles->contains('name', 'Management') || auth('sanctum')->user()->roles->contains('name', 'Marketing'))
            {
                $managementId = auth('sanctum')->user()->id;
            }

        }

        // Customer Attachment
        if($request->file('customer_attachment'))
        {
            $customerAttachment =  time().'.'.$request->file('customer_attachment')->extension();

            $customerAttachmentPath = 'customer_attachment/' . $customerAttachment;

            Storage::disk('public')->put($customerAttachmentPath, file_get_contents($request->file('customer_attachment')));

            $customerAttachmentFile = $customerAttachment;
        }
        else {
            $customerAttachmentFile = null;
        }

        return new Customer(
            id: $customer_id,
            nric: isset($request->nric) ? $request->nric : '',
            attachment: $customerAttachmentFile,
            status: 1,
            source: $request->source ?? null,
            additional_information: $request->additional_information,
            assigned_by_management_id: $managementId,
            user_id:$user_id,
            last_modified_by: auth('sanctum')->user()->id ?? 1, // for register set done by system mean superadmin
            company_name: $request->company_name,
            customer_type: $request->customer_type,
            budget: $request->budget,
            quote_value: $request->quote_value ?? null,
            book_value: $request->book_value ?? null,
            key_collection: $request->key_collection,
            id_milestone_id: $request->id_milestone_id,
            rejected_reason_id: $request->rejected_reason_id ?? null,
            next_meeting: $request->next_meeting,
            days_aging: $request->days_aging ?? null,
            remarks: $request->remarks,
            budget_value: $request->budget_value ?? null,
        );
    }

    public static function fromEloquent(CustomerEloquentModel $customerEloquent): Customer
    {
        return new Customer(
            id: $customerEloquent->id,
            nric: $customerEloquent->nric,
            attachment: $customerEloquent->attachment,
            source: $customerEloquent->source ?? null,
            status: $customerEloquent->status,
            additional_information: $customerEloquent->additional_information,
            assigned_by_management_id: $customerEloquent->assigned_by_management_id,
            user_id: $customerEloquent->user_id,
            last_modified_by: $customerEloquent->last_modified_by,
            company_name:  $customerEloquent->company_name,
            customer_type:  $customerEloquent->customer_type,
            budget: $customerEloquent->budget ?? null,
            quote_value: $customerEloquent->quote_value ?? null,
            book_value: $customerEloquent->book_value ?? null,
            key_collection: $customerEloquent->key_collection,
            id_milestone_id: $customerEloquent->id_milestone_id,
            rejected_reason_id: $customerEloquent->rejected_reason_id ?? null,
            next_meeting: $customerEloquent->next_meeting,
            days_aging: $customerEloquent->days_aging ?? null,
            remarks: $customerEloquent->remarks,
            budget_value: $customerEloquent->budget_value ?? null
        );
    }

    public static function toEloquent(Customer $customer): CustomerEloquentModel
    {

        $customerEloquent = new CustomerEloquentModel();

        if ($customer->id) {
            $customerEloquent = CustomerEloquentModel::query()->where('id',$customer->id)->first();
        }

        $customerEloquent->nric = $customer->nric;
        $customerEloquent->attachment = $customer->attachment;
        $customerEloquent->status = $customer->status;
        $customerEloquent->source = $customer->source ?? null;
        $customerEloquent->additional_information = $customer->additional_information;
        $customerEloquent->assigned_by_management_id = $customer->assigned_by_management_id;
        $customerEloquent->user_id = $customer->user_id;
        $customerEloquent->last_modified_by = $customer->last_modified_by;
        $customerEloquent->company_name = $customer->company_name;
        $customerEloquent->customer_type = $customer->customer_type;
        $customerEloquent->budget = $customer->budget;
        $customerEloquent->quote_value = $customer->quote_value;
        $customerEloquent->book_value = $customer->book_value;
        $customerEloquent->key_collection = $customer->key_collection;
        $customerEloquent->id_milestone_id = $customer->id_milestone_id;
        $customerEloquent->rejected_reason_id = $customer->rejected_reason_id;
        $customerEloquent->next_meeting = $customer->next_meeting;
        $customerEloquent->days_aging = $customer->days_aging;
        $customerEloquent->remarks = $customer->remarks;
        $customerEloquent->budget_value = $customer->budget_value;

        return $customerEloquent;
    }
}
