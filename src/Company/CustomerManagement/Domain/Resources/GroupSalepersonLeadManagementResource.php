<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Carbon\Carbon;
use Src\Company\System\Domain\Resources\AssignSalepersonsResources;
use stdClass;

class GroupSalepersonLeadManagementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name_prefix' => $this->name_prefix,
            'total_book_value' => $this->total_book_value,
            'total_quote_value' => $this->total_quote_value,
            'total_budget_value' => $this->total_budget_value,
            'assigned_salepersons' => AssignSalepersonsResources::collection($this->assignedSalepersons)
        ];
    }
}
