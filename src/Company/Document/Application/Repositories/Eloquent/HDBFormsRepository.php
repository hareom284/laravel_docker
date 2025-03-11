<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\DTO\HDBFormsData;
use Src\Company\Document\Application\Mappers\HDBFormsMapper;
use Src\Company\Document\Domain\Model\Entities\HDBForms;
use Src\Company\Document\Domain\Repositories\HDBFormsRepositoryInterface;
use Src\Company\Document\Domain\Resources\HDBResource;
use Src\Company\Document\Infrastructure\EloquentModels\HDBFormsEloquentModel;

class HDBFormsRepository implements HDBFormsRepositoryInterface
{

    public function getHDBForms($project_id)
    {

        $hdbFormsEloquent = HDBFormsEloquentModel::query()->where('project_id', $project_id)->get();

        $result = HDBResource::collection($hdbFormsEloquent);
        
        return $result;
    }

    public function findHDBFormsById(int $id)
    {
        $hdbFormsEloquent = HDBFormsEloquentModel::query()->findOrFail($id);

        $result = new HDBResource($hdbFormsEloquent);

        return $result;
    }

    public function store(HDBForms $hDBForms): HDBFormsData
    {

        $hDBFormsEloquent = HDBFormsMapper::toEloquent($hDBForms);

        $hDBFormsEloquent->save();

        return HDBFormsData::fromEloquent($hDBFormsEloquent);
    }

    public function update(HDBForms $hdbForms): HDBFormsData
    {
        $hdbFormsEloquent = HDBFormsMapper::toEloquent($hdbForms);

        $hdbFormsEloquent->save();

        return HDBFormsData::fromEloquent($hdbFormsEloquent);
    }

    public function delete(int $hdb_forms_id): void
    {
        $hdbFormsEloquent = HDBFormsEloquentModel::query()->findOrFail($hdb_forms_id);

        if($hdbFormsEloquent->document_file)
        {
            if(Storage::disk('public')->exists('hdb_forms_file/' . $hdbFormsEloquent->document_file)){

                Storage::disk('public')->delete('hdb_forms_file/' . $hdbFormsEloquent->document_file);

            }
        }

        $hdbFormsEloquent->delete();
    }
}