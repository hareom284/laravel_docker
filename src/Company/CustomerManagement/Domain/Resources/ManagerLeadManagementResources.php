<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Carbon\Carbon;
use stdClass;

class ManagerLeadManagementResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->customers->next_meeting) {

            $nextMeetingDate = Carbon::createFromFormat('Y-m-d', $this->customers->next_meeting);
            $todayDate = Carbon::now();
            if ($todayDate->greaterThan($nextMeetingDate)) {
                $daysDifference = $todayDate->diffInDays($nextMeetingDate);
            }else{
                $daysDifference = '-' . $todayDate->diffInDays($nextMeetingDate);
            }
        } else {
            $daysDifference = '-';
        }

        if (isset($this->customers->staffs)) {

            $assign_staffs = [];

            // $assign_staffs = collect($this->customers->staffs)->pluck('user_id')->toArray();

            foreach ($this->customers->staffs as $value) {
                $obj = new stdClass();
                $obj->id = $value->user->id;
                $obj->name = $value->user->first_name . ' ' . $value->user->last_name;

                array_push($assign_staffs, $obj);
            }
        } else {
            $assign_staffs = [];
        }

        return [
            'id' => $this->id,
            'name_prefix' => $this->name_prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'contact_no' => $this->contact_no,
            'is_active' => $this->is_active,
            'customer_table_id' => $this->customers->id,
            'customer_status' => $this->customers->status,
            'nric_num' => $this->customers->nric,
            'source' => $this->customers->source ?? '-',
            'additional_info' => $this->customers->additional_information,
            'create_account' => $this->password != null ? false : true,
            'created_at' => Carbon::parse($this->created_at)->format('d F Y'),
            'month' => Carbon::parse($this->created_at)->format('M Y'),
            'inactive_reason' => $this->customers->inactive_reason,
            'company_name' => $this->customers->company_name,
            'customer_type' => $this->customers->customer_type,
            'budget' => $this->customers->budget ?? '-',
            'budget_value' => $this->customers->budget_value ?? 0,
            'quote_value' => $this->customers->quote_value ?? 0,
            'book_value' => $this->customers->book_value ?? 0,
            'key_collection' => $this->customers->key_collection,
            'id_milestone' => $this->customers->currentIdMilestone ? $this->customers->currentIdMilestone->name : '-',
            'rejected_reason' => $this->customers->rejectedReason ? $this->customers->rejectedReason->name: '-',
            'next_meeting' => $this->customers->next_meeting,
            'days_aging' => $daysDifference,
            'remarks' => $this->customers->remarks,
            'assign_staff' => count($assign_staffs) > 0 ? $assign_staffs[0]->name : '',
            'cx_count' => count($this->projectPivot) > 0 ? count($this->projectPivot[0]->customersPivot) : null
        ];
    }
}
