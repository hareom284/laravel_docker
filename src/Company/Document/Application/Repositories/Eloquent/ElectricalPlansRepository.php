<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\DTO\ElectricalPlansData;
use Src\Company\Document\Application\Mappers\ElectricalPlansMapper;
use Src\Company\Document\Domain\Model\Entities\ElectricalPlans;
use Src\Company\Document\Domain\Repositories\ElectricalPlansRepositoryInterface;
use Src\Company\Document\Domain\Resources\ElectricalPlansResource;
use Src\Company\Document\Infrastructure\EloquentModels\ElectricalPlansEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\MaterialEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class ElectricalPlansRepository implements ElectricalPlansRepositoryInterface
{

    public function getElectricalPlans($projectId)
    {

        $electricalPlansEloquent = ElectricalPlansEloquentModel::query()->where('project_id', $projectId)->get();

        $electricalPlans = ElectricalPlansResource::collection($electricalPlansEloquent);

        return $electricalPlans;
    }

    // public function findDesignWorkById(int $id)
    // {
    //     $designWorkEloquent = DesignWorkEloquentModel::query()->with('materials','designer.user','assistantDesigner.user','project.properties','project.salespersons','project.customers')->findOrFail($id);
    //     return $designWorkEloquent;
    // }

    public function store(ElectricalPlans $electricalPlans, $salesperson_id, $materials): ElectricalPlansData
    {

        $electricalPlansEloquent = ElectricalPlansMapper::toEloquent($electricalPlans);

        $electricalPlansEloquent->save();

        foreach ($materials as $material) {

            $material_find = MaterialEloquentModel::firstOrCreate(
                ['name' => $material['name']],
                ['is_predefined' => false]
            );

            $electricalPlansEloquent->materials()->attach($material_find->id, ['color_code' => $material['color_code'] ] );
        }

        foreach ($salesperson_id as $saleperson) {

            $staff = StaffEloquentModel::query()->where('user_id', $saleperson)->first();

            $electricalPlansEloquent->assistantDesigner()->attach($staff->id);
        }

        return ElectricalPlansData::fromEloquent($electricalPlansEloquent);
    }
    // public function update(DesignWork $designWork, $salepersons_id, $materials): DesignWork
    // {

    //     // $authUser =auth('sanctum')->user();

    //     // $authStaff = StaffEloquentModel::query()->where('user_id', $authUser->id)->first();

    //     $designWorkArray = $designWork->toArray();

    //     $designWorkEloquent = DesignWorkEloquentModel::query()->findOrFail($designWork->id);

    //     // $designWorkEloquent->designer_in_charge_id = $designWorkEloquent->designer_in_charge_id;

    //     $designWorkEloquent->fill($designWorkArray);

    //     $designWorkEloquent->save();

    //     $materialSyncData = [];

    //     $salepersonSyncData = [];

    //     foreach ($materials as $material) {

    //         $material_find = MaterialEloquentModel::firstOrCreate(
    //             ['name' => $material['name']],
    //             ['is_predefined' => false]
    //         );

    //         $materialSyncData[$material_find->id] = ['color_code' => $material['color_code']];
    //     }

    //     $designWorkEloquent->materials()->sync($materialSyncData);

    //     foreach ($salepersons_id as $saleperson) {
    //         $staff = StaffEloquentModel::query()->where('user_id', $saleperson)->first();

    //         if ($staff) {
    //             $salepersonSyncData[] = $staff->id;
    //             // array_push($salepersonSyncData, $staff->id);
    //         }
    //     }

    //     $designWorkEloquent->assistantDesigner()->sync($salepersonSyncData);

    //     return $designWork;
    // }

    public function delete(int $electrical_plans_id): void
    {
        $electricalPlansEloquent = ElectricalPlansEloquentModel::query()->findOrFail($electrical_plans_id);

        if($electricalPlansEloquent->document_file)
        {
            if(Storage::disk('public')->exists('electrical_plans_file/' . $electricalPlansEloquent->document_file)){

                Storage::disk('public')->delete('electrical_plans_file/' . $electricalPlansEloquent->document_file);

            }
        }

        $electricalPlansEloquent->materials()->detach();

        $electricalPlansEloquent->assistantDesigner()->detach();

        $electricalPlansEloquent->delete();
    }
}
