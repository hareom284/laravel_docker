<?php

namespace Src\Company\System\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class SiteSetting extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $site_name,
        public readonly ?string $ssl,
        public readonly ?string $timezone,
        public readonly ?string $locale,
        public readonly ?string $url,
        public readonly ?string $email,
        public readonly ?string $contact_number,
        public readonly ?string $website_logo,
        public readonly ?string $website_favicon
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'site_name' => $this->site_name,
            'ssl' => $this->ssl,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'url' => $this->url,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'website_logo' => $this->website_logo,
            'website_favicon' => $this->website_favicon        ];
    }
}
