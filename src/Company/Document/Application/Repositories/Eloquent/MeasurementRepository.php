<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\Mappers\MeasurementMapper;
use Src\Company\Document\Domain\Repositories\MeasurementRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\MeasurementEloquentModel;
use Src\Company\Document\Domain\Resources\MeasurementResource;

class MeasurementRepository implements MeasurementRepositoryInterface
{
    public function getAll()
    {
        $measurementEloqument = MeasurementEloquentModel::all();

        $measurements = MeasurementResource::collection($measurementEloqument);
        
        return $measurements;
    }

    public function store(array $measurements): array
    {  
        $measurementEloqument = MeasurementEloquentModel::all();

        if(count($measurementEloqument) > 0)
        {
            foreach ($measurementEloqument as $measurement) {
                $measurement->delete();
            }
        }

        return DB::transaction(function () use ($measurements) {

            $measurementEloquents = [];

            foreach($measurements as $measurement)
            {
                $measurementEloquent = MeasurementMapper::toEloquent($measurement);

                $measurementEloquent->save();

                $measurementEloquents[] = $measurementEloquent;
            }

            return $measurementEloquents;
        });
    }

}