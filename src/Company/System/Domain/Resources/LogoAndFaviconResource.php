<?php

namespace Src\Company\System\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogoAndFaviconResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $logoUrl = $this->website_logo ?? null;

        $faviconUrl = $this->website_favicon ??  null;

        return [
            'id' => $this->id,
            'website_logo' => $logoUrl,
            'website_favicon' => $faviconUrl,
            'site_name' => $this->site_name
        ];
    }
}
