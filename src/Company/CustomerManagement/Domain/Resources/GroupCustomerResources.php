<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Carbon\Carbon;
use stdClass;

class GroupCustomerResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->next_meeting) {

            $nextMeetingDate = Carbon::createFromFormat('Y-m-d', $this->next_meeting);
            $todayDate = Carbon::now();
            if ($todayDate->greaterThan($nextMeetingDate)) {
                $daysDifference = $todayDate->diffInDays($nextMeetingDate);
            }else{
                $daysDifference = '-' . $todayDate->diffInDays($nextMeetingDate);
            }
        } else {
            $daysDifference = '-';
        }

        if(count($this->user->projectPivot) > 0){
            $cx_count = count($this->user->projectPivot[0]->customersPivot);
        } else {
            $cx_count = 1;
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'name_prefix' => $this->user->name_prefix,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'source' => $this->source ?? '-',
            'month' => Carbon::parse($this->user->created_at)->format('M Y'),
            'budget' => $this->budget ?? '-',
            'budget_value' => $this->budget_value ?? 0,
            'quote_value' => $this->quote_value ?? 0,
            'book_value' => $this->book_value ?? 0,
            'key_collection' => $this->key_collection,
            'id_milestone' => $this->currentIdMilestone ? $this->currentIdMilestone->name : '-',
            'rejected_reason' => $this->rejectedReason ? $this->rejectedReason->name: '-',
            'next_meeting' => $this->next_meeting,
            'days_aging' => $daysDifference,
            'remarks' => $this->remarks,
            'cx_count' => $cx_count,
        ];
    }
}
