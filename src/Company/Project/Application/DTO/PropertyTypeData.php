<?php

namespace Src\Company\Project\Application\DTO;

class PropertyTypeData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $type,
        public readonly int $is_predefined
    )
    {}
}