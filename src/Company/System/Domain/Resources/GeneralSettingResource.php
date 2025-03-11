<?php

namespace Src\Company\System\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $value = "";
        if($this->setting == 'whatsapp_access_token' && $this->value){
            $value = '******************************************************';
        }else{
            $value = $this->value;
        }
        return [
            'setting' => $this->setting,
            'value' => $value,
            'is_array' => $this->is_array ?? false
        ];
    }
}
