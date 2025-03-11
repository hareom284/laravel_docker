<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\DesignWorkData;
use Src\Company\Document\Application\Mappers\DesignWorkMapper;
use Src\Company\Document\Domain\Model\Entities\DesignWork;
use Src\Company\Document\Domain\Resources\DesignWorkResource;
use Src\Company\Document\Domain\Repositories\DesignWorkRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\DesignWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\MaterialEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\Document\Domain\Resources\DesignWorkDetailResource;

class DesignWorkRepository implements DesignWorkRepositoryInterface
{

    public function getDesignWorks($projectId)
    {

        $designWorkEloquent = DesignWorkEloquentModel::query()->where('project_id',$projectId)->get();

        $designWorks = DesignWorkResource::collection($designWorkEloquent);

        return $designWorks;
    }

    public function findDesignWorkById(int $id)
    {
        $designWorkEloquent = DesignWorkEloquentModel::query()->with('materials','designer.user','assistantDesigner.user','project.properties','project.salespersons','project.customers')->findOrFail($id);

        $designWorkData = new DesignWorkDetailResource($designWorkEloquent);

        return $designWorkData;
    }

    public function store(DesignWork $designWork, $salepersons_id, $materials): DesignWorkData
    {

        $authUser =auth('sanctum')->user();

        $authStaff = StaffEloquentModel::query()->where('user_id', $authUser->id)->first();

        $designWorkEloquent = DesignWorkMapper::toEloquent($designWork);

        $designWorkEloquent->designer_in_charge_id = $authStaff->id;

        $designWorkEloquent->save();

        if(count($materials) > 0)
        {
            foreach ($materials as $material) {

                $material_find = MaterialEloquentModel::firstOrCreate(
                    ['name' => $material->name],
                    ['is_predefined' => false]
                );

                $designWorkEloquent->materials()->attach($material_find->id, ['color_code' => $material->color_code ] );
            }
        }

        if(count($salepersons_id) > 0)
        {
            foreach ($salepersons_id as $saleperson) {

                $staff = StaffEloquentModel::query()->where('user_id',$saleperson)->first();

                $designWorkEloquent->assistantDesigner()->attach($staff->id);
            }
        }

        return DesignWorkData::fromEloquent($designWorkEloquent);
    }
    public function update(DesignWork $designWork, $salepersons_id, $materials): DesignWork
    {

        // $authUser =auth('sanctum')->user();

        // $authStaff = StaffEloquentModel::query()->where('user_id', $authUser->id)->first();

        $designWorkArray = $designWork->toArray();

        $designWorkEloquent = DesignWorkEloquentModel::query()->findOrFail($designWork->id);

        // $designWorkEloquent->designer_in_charge_id = $designWorkEloquent->designer_in_charge_id;

        $designWorkEloquent->fill($designWorkArray);

        $designWorkEloquent->save();

        $materialSyncData = [];

        $salepersonSyncData = [];

        foreach ($materials as $material) {

            $material_find = MaterialEloquentModel::firstOrCreate(
                ['name' => $material->name],
                ['is_predefined' => false]
            );

            $materialSyncData[$material_find->id] = ['color_code' => $material->color_code];
        }

        $designWorkEloquent->materials()->sync($materialSyncData);

        foreach ($salepersons_id as $saleperson) {
            $staff = StaffEloquentModel::query()->where('user_id', $saleperson)->first();

            if ($staff) {
                $salepersonSyncData[] = $staff->id;
                // array_push($salepersonSyncData, $staff->id);
            }
        }

        $designWorkEloquent->assistantDesigner()->sync($salepersonSyncData);

        return $designWork;
    }

    public function delete(int $design_work_id): void
    {
        $designWorkEloquent = DesignWorkEloquentModel::query()->findOrFail($design_work_id);

        $designWorkEloquent->delete();
    }
}
