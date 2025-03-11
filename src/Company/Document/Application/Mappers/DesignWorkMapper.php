<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\DesignWork;
use Src\Company\Document\Infrastructure\EloquentModels\DesignWorkEloquentModel;
use Illuminate\Support\Facades\Storage;


class DesignWorkMapper
{
    public static function fromRequest(Request $request, ?int $design_work_id = null): DesignWork
    {

        if($request->hasFile('document_file'))
        {
            $fileName =  time().'.'.$request->document_file->getClientOriginalExtension();

            $filePath = 'design_work_file/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

            $documentFile = $fileName;
        } else {
            $documentFile = $request->original_document ? $request->original_document : null;
        }

        return new DesignWork(
            id: $design_work_id,
            date: $request->date,
            name: $request->name,
            document_file: $documentFile,
            scale: $request->scale ? $request->scale : null,
            designer_in_charge_id: $request->designer_in_charge_id,
            project_id: $request->project_id
        );
    }

    public static function fromEloquent(DesignWorkEloquentModel $designWorkEloquent): DesignWork
    {
        return new DesignWork(
            id: $designWorkEloquent->id,
            date: $designWorkEloquent->date,
            name: $designWorkEloquent->name,
            document_file: $designWorkEloquent->document_file,
            scale: $designWorkEloquent->scale,
            designer_in_charge_id: $designWorkEloquent->designer_in_charge_id,
            project_id: $designWorkEloquent->project_id
        );
    }

    public static function toEloquent(DesignWork $designWork): DesignWorkEloquentModel
    {
        $currentDate = Carbon::now();

        $designWorkEloquent = new DesignWorkEloquentModel();
        if ($designWork->id) {
            $designWorkEloquent = DesignWorkEloquentModel::query()->findOrFail($designWork->id);
        }
        $designWorkEloquent->date = $currentDate;
        $designWorkEloquent->document_date = $designWork->date;
        $designWorkEloquent->name = $designWork->name;
        $designWorkEloquent->document_file = $designWork->document_file;
        $designWorkEloquent->scale = $designWork->scale;
        $designWorkEloquent->request_status = 0; //0 => none, 1 =>requested, 2 => received
        $designWorkEloquent->last_edited = $currentDate;
        $designWorkEloquent->signature = null;
        $designWorkEloquent->signed_date = $designWork->id ? $currentDate : null;
        $designWorkEloquent->designer_in_charge_id = $designWork->designer_in_charge_id;
        $designWorkEloquent->project_id = $designWork->project_id;

        return $designWorkEloquent;
    }
}