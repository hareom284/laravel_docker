<?php

namespace Src\Company\Project\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EvoItemScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $evoItems = $this->evo_items;

        $totalItems = 0;
        $checkItems = 0;
        $minStartDate = null;
        $maxEndDate = null;

        foreach ($evoItems as $evoItem) {

            $totalItems += $evoItem->rooms->count();
            $checkItems += $evoItem->rooms()->wherePivot('is_checked',1)->count();

            $evoItemMinStartDate = $evoItem->rooms()->min('start_date');
            $evoItemMaxEndDate = $evoItem->rooms()->max('end_date');

            // Update the minimum start date if it's earlier than the current minimum
            if ($minStartDate === null || $evoItemMinStartDate < $minStartDate) {
                $minStartDate = $evoItemMinStartDate;
            }

            // Update the maximum end date if it's later than the current maximum
            if ($maxEndDate === null || $evoItemMaxEndDate > $maxEndDate) {
                $maxEndDate = $evoItemMaxEndDate;
            }
        }

        return [
            'id' => $this->id,
            'version_number' => $this->version_number,
            'total_items' => $totalItems,
            'check_items' => $checkItems,
            'evo_items' => $evoItems,
            'start_date' => $minStartDate,
            'end_date' => $maxEndDate,
        ];
    }
}
