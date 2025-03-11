<?php

namespace Src\Company\CustomerManagement\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ReferrerFormResource extends JsonResource
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
            'owner' => $this->owner ? $this->owner : null,
            'referrer' => $this->referrer ? $this->referrer : null,
            'referrer_properties' => $this->referrer_properties ? $this->referrer_properties : null,
            'signed_salesperson' => $this->salesperson ? $this->salesperson : null,
            'signed_management' => $this->management ? $this->management : null,
            'owner_signature' => $this->getSignatureAsBase64($this->owner_signature, 'referrer_form'),
            'salesperson_signature' => $this->getSignatureAsBase64($this->salesperson_signature, 'referrer_form'),
            'management_signature' => $this->getSignatureAsBase64($this->management_signature, 'referrer_form'),
            'date_of_referral' => $this->date_of_referral,
            'owner_file_name' => $this->owner_signature,
            'salesperson_file_name' => $this->salesperson_signature,
            'management_file_name' => $this->management_signature,
        ];
    }

    function getSignatureAsBase64(?string $fileName, string $directory, string $disk = 'public'): ?string
    {
        if ($fileName) {
            $filePath = $directory . '/' . $fileName;
            if (Storage::disk($disk)->exists($filePath)) {
                $fileContent = Storage::disk($disk)->get($filePath);
                return base64_encode($fileContent);
            }
        }
        return null;
    }
}
