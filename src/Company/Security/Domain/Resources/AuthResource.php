<?php

namespace Src\Company\Security\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Src\Company\Security\Domain\Resources\AuthPermissionResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = "";
        $haveImage = $this->image->count();
        if ($haveImage > 0) {
            $image = $this->image[0]->original_url;
        }
        return [
            "id" => $this->id ?? "",
            "name" => $this->name  ?? "",
            "email" => $this->email ?? "",
            "roles" => $this->roles ?? "",
            "image" =>  $image,
            "permissions" => $this->roles ?  new AuthPermissionResource($this->roles()->with('permissions')->first()) : ""
        ];
    }
}
