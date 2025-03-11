<?php

namespace Src\Company\Project\Application\DTO;

class PropertyData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $type_id,
        public readonly ?string $street_name,
        public readonly ?string $block_num,
        public readonly ?string $unit_num,
        public readonly ?string $postal_code,
    )
    {}
}