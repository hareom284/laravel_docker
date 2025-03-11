<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Model\Entities\ElectricalPlans;
use Src\Company\Document\Infrastructure\EloquentModels\ElectricalPlansEloquentModel;

class ElectricalPlansMapper
{
    public static function fromRequest(Request $request, ?int $electrical_plan_id = null): ElectricalPlans
    {
        if($request->hasFile('document_file'))
        {
            $fileName =  time().'.'.$request->file('document_file')->extension();

            $filePath = 'electrical_plans_file/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->file('document_file')));

        }else{

            $fileName = '';

        }

        return new ElectricalPlans(
            id: $electrical_plan_id,
            document_file: $fileName,
            customer_signature: $request->string('customer_signature'),
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(ElectricalPlansEloquentModel $electricalPlansEloquent): ElectricalPlans
    {
        return new ElectricalPlans(
            id: $electricalPlansEloquent->id,
            document_file: $electricalPlansEloquent->document_file,
            customer_signature: $electricalPlansEloquent->customer_signature,
            project_id: $electricalPlansEloquent->project_id
        );
    }

    public static function toEloquent(ElectricalPlans $electricalPlans): ElectricalPlansEloquentModel
    {
        $currentDate = Carbon::now();

        $document_file = $electricalPlans->document_file;

        $electricalPlansEloquent = new ElectricalPlansEloquentModel();
        if ($electricalPlans->id) {
            $electricalPlansEloquent = ElectricalPlansEloquentModel::query()->findOrFail($electricalPlans->id);

            if($electricalPlans->document_file)
            {
                 if(Storage::disk('public')->exists('electrical_plans_file/' . $electricalPlansEloquent->document_file)){

                    Storage::disk('public')->delete('electrical_plans_file/' . $electricalPlansEloquent->document_file);

                }
            }

            $document_file = $electricalPlans->document_file == null ? $electricalPlansEloquent->document_file : $document_file;
        }
        $electricalPlansEloquent->date_uploaded = $currentDate;
        $electricalPlansEloquent->document_file = $document_file;
        $electricalPlansEloquent->customer_signature = $electricalPlans->customer_signature;
        $electricalPlansEloquent->project_id = $electricalPlans->project_id;

        return $electricalPlansEloquent;
    }
}