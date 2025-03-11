<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Carbon\Carbon;
use stdClass;

class CustomerWithEmailResource extends JsonResource
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
            'user_id' => $this->id,
            'name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'customer_id' => $this->customers->id,
            'email_with_name' => $this->first_name . ' ' . $this->last_name . ' ' . '(' . $this->email . ')'
        ];
    }
}
