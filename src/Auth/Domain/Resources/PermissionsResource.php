<?php

namespace Src\Auth\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PermissionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $result = collect($this->permissions)->map(function ($action) {
            list($action, $subject) = explode('_', $action, 2);
            return ['action' => $action, 'subject' => $subject];
        });

        return $result;

    }

}
