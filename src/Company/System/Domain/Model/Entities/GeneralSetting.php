<?php

namespace Src\Company\System\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class GeneralSetting extends Entity
{
    public function __construct(
        public readonly string $setting,
        public readonly string $value,
        public readonly ?bool $is_array
    ) {}



    public function toArray(): array
    {
        return [
            'setting' => $this->setting,
            'value' => $this->value,
            'is_array' => $this->is_array];
    }
}
