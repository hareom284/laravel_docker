<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Carbon\Carbon;
use stdClass;

class CustomerMobileResource extends JsonResource
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
        $profileUrl = $this->profile_pic ? asset('storage/profile_pic/' . $this->profile_pic) : null;

        if (isset($this->customers->staffs)) {

            $assign_staffs = [];
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

        return [
            'id' => $this->id,
            'name_prefix' => $this->name_prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'fullName' => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')),
            'email' => $this->email,
            'contact_no' => $this->prefix.''.$this->contact_no,
            'profile_pic' => $profileUrl,
            'is_active' => $this->is_active,
            'customer_table_id' => $this->customers->id,
            'customer_status' => $this->customers->status,
            'nric_num' => $this->customers->nric,
            'budget' => $this->customers->budget,
            'create_account' => $this->password != null ? false : true,
            'assign_staffs' => $assign_staffs,
            'assign_by' => $assign_by,
            'lead_properties' => LeadPropertyResource::collection($this->customers->customer_properties),
            'created_at' => Carbon::parse($this->created_at)->format('d M Y'),
            'current_milestone_id' => isset($this->customers->currentIdMilestone) ? $this->customers->currentIdMilestone->id : '',
            'current_milestone' => isset($this->customers->currentIdMilestone) ? $this->customers->currentIdMilestone->name : '',
            'hasCredential' => $this->password ? true : false,
            'key_collection' => $this->customers->key_collection,
            'next_meeting' => $this->customers->next_meeting,
            'hasProject' => isset($this->customer_project) ? true : false
        ];
    }
}
