<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\Measurement;
use Src\Company\Document\Infrastructure\EloquentModels\MeasurementEloquentModel;

class MeasurementMapper
{
    public static function fromRequest(array $measurementsData, ?int $measurement_id = null): array
    {
        $measurements = [];

        foreach ($measurementsData as $data) {

            $measurement = new Measurement(
                id: $measurement_id,
                name: $data['name'],
                fixed: $data['fixed']
            );

            $measurements[] = $measurement;
        }

        return $measurements;
    }

    public static function toEloquent(Measurement $measurement): MeasurementEloquentModel
    {

        $measurementEloquent = new MeasurementEloquentModel();
        if ($measurement->id) {

            $measurementEloquent = MeasurementEloquentModel::query()->findOrFail($measurement->id);

        }
        $measurementEloquent->name = $measurement->name;
        $measurementEloquent->fixed = $measurement->fixed;

        return $measurementEloquent;
    }
}