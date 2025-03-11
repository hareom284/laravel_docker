<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Carbon\Carbon;
use stdClass;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (!isset($this->customers))
            return [];
        //checking existing property id use in project
        $project_property_id = [];


        $projects = ProjectEloquentModel::get(['property_id']);
        $projects_new = ProjectEloquentModel::with(['customersPivot' => function ($query) {
            $query->select('property_id');
        }])->get();

        $propertyIds = $projects_new->pluck('customersPivot')->collapse()->pluck('property_id')->reject(function ($value) {
            return $value === null;
        })->unique()->toArray();


        foreach ($projects as $project) {
            array_push($project_property_id, $project->property_id);
        };

        $unique_property_ids = array_unique(array_merge($propertyIds, $project_property_id));
        // $filter = $this->customers->customer_properties->filter(function ($item) use ($unique_property_ids) {
        //     return !in_array($item->pivot->property_id, $unique_property_ids);
        // });
        //end check

        $profileUrl = $this->profile_pic ? asset('storage/profile_pic/' . $this->profile_pic) : null;

        // $address = isset($this->customer_project) ? $this->customer_project->property->block_num .' '. $this->customer_project->property->street_name .' #'. $this->customer_project->property->unit_num .' '. $this->customer_project->property->postal_code : null;

        $countProject = ProjectEloquentModel::whereHas('customersPivot', function ($query) {
            $query->where('user_id', $this->customers->user_id);
        })->count();

        if (isset($this->customers->staffs)) {

            $assign_staffs = [];

            // $assign_staffs = collect($this->customers->staffs)->pluck('user_id')->toArray();

            foreach ($this->customers->staffs as $value) {
                $obj = new stdClass();
                $obj->id = $value->user->id;
                $obj->profile_pic = $value->user->profile_pic ? asset('storage/profile_pic/' .  $value->user->profile_pic) : null;
                $obj->name = $value->user->first_name . ' ' . $value->user->last_name;

                array_push($assign_staffs, $obj);
            }
        } else {
            $assign_staffs = [];
        }

        if (isset($this->customers->assign_staff)) {
            $assign_by = $this->customers->assign_staff->first_name . ' ' . $this->customers->assign_staff->last_name;
        } else {
            $assign_by = null;
        }
        foreach ($this->customers->customer_properties as $property) {
            // Check if property_id is in the $properties_ids array
            $property->isDisabled = in_array($property->pivot->property_id, $unique_property_ids);
        }

        $documentUrl = $this->customers->attachment ? asset('storage/customer_attachment/' . $this->customers->attachment) : null;

        $extension = $this->customers->attachment ? pathinfo($documentUrl, PATHINFO_EXTENSION) : null;

        return [
            'id' => $this->id,
            'name_prefix' => $this->name_prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'contact_no' => $this->contact_no,
            'profile_pic' => $profileUrl,
            'profile_image' => $this->profile_pic, // don't remove this, reused in lead update
            'is_active' => $this->is_active,
            'customer_table_id' => $this->customers->id,
            'customer_status' => $this->customers->status,
            'nric_num' => $this->customers->nric,
            'source' => $this->customers->source ?? null,
            'additional_info' => $this->customers->additional_information,
            'check_lists' => $this->customers->check_lists,
            'create_account' => $this->password != null ? false : true,
            'assign_staffs' => $assign_staffs,
            'assign_by' => $assign_by,
            'count_project' => $countProject,
            'lead_properties_for_customer' => LeadPropertyResource::collection($this->customers->customer_properties),
            'lead_properties' => LeadPropertyResource::collection($this->customers->customer_properties),
            'created_at' => Carbon::parse($this->created_at)->format('d F Y'),
            'lead_check_lists' => LeadCheckListResource::collection($this->customers->leadCheckLists),
            'inactive_at' => $this->customers->inactive_at ? Carbon::parse($this->customers->inactive_at)->format('d F Y') : '',
            'inactive_reason' => $this->customers->inactive_reason,
            'company_name' => $this->customers->company_name,
            'customer_type' => $this->customers->customer_type,
            'budget' => $this->customers->budget,
            'quote_value' => $this->customers->quote_value,
            'book_value' => $this->customers->book_value,
            'key_collection' => $this->customers->key_collection,
            'id_milestone_id' => $this->customers->id_milestone_id,
            'rejected_reason_id' => $this->customers->rejected_reason_id,
            'next_meeting' => $this->customers->next_meeting,
            'days_aging' => $this->customers->days_aging,
            'remarks' => $this->customers->remarks,
            'budget_value' => $this->customers->budget_value,
            'customer_attachment' => $documentUrl,
            'attachment_extension' => $extension,
            'attachment_name' => $this->customers->attachment ? $this->customers->attachment : null,
            'relation_with_referrer' => $this->relation_with_referrer 
        ];
    }
}
