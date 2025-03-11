<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\MeasurementEloquentModel;

class MeasurementData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly int $fixed,
    )
    {}
}