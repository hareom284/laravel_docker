<?php

namespace Src\Company\System\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $logoUrl = $this->website_logo ? asset('storage/website_logo/' . $this->website_logo) : null;

        // $faviconUrl = $this->website_favicon ? asset('storage/website_favicon/' . $this->website_favicon) : null;

        $logoUrl = $this->website_logo ? $this->website_logo : null;

        $faviconUrl = $this->website_favicon ? $this->website_favicon : null;
        
        return [
            'id' => $this->id,
            'site_name' => $this->site_name,
            'ssl' => $this->ssl ?? '-',
            'timezone' => $this->timezone ?? '-',
            'locale' => $this->locale ?? '-',
            'url' => $this->url ?? '-',
            'email' => $this->email ?? '-',
            'contact_number' => $this->contact_number ?? '-',
            'website_logo' => $logoUrl,
            'website_favicon' => $faviconUrl
        ];
    }
}
