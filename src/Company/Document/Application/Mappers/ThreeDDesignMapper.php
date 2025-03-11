<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\ThreeDDesign;
use Src\Company\Document\Infrastructure\EloquentModels\ThreeDDesignEloquentModel;
use Illuminate\Support\Facades\Storage;


class ThreeDDesignMapper
{
    public static function fromRequest(Request $request, ?int $design_id = null): ThreeDDesign
    {

        if($request->hasFile('document_file'))
        {
            $fileName =  time().'.'.$request->document_file->getClientOriginalExtension();

            $filePath = '3d_design/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

            $documentFile = $fileName;
        } else {
            $documentFile = $request->original_document ? $request->original_document : null;
        }

        return new ThreeDDesign(
            id: $design_id,
            name: $request->name,
            date: $request->date,
            last_edited: $request->last_edited,
            document_file: $documentFile,
            project_id: $request->project_id,
            design_work_id: $request->design_work_id,
            uploader_id: $request->uploader_id
        );
    }

    public static function fromEloquent(ThreeDDesignEloquentModel $designEloquent): DesignWork
    {
        return new DesignWork(
            id: $designEloquent->id,
            name: $designEloquent->name,
            date: $designEloquent->date,
            last_edited: $designEloquent->last_edited,
            document_file: $designEloquent->document_file,
            project_id: $designEloquent->project_id,
            design_work_id: $designEloquent->design_work_id,
            uploader_id: $designEloquent->uploader_id
        );
    }

    public static function toEloquent(ThreeDDesign $design): ThreeDDesignEloquentModel
    {
        $currentDate = Carbon::now();

        $designEloquent = new ThreeDDesignEloquentModel();
        if ($design->id) {
            $designEloquent = ThreeDDesignEloquentModel::query()->findOrFail($design->id);
        }
        $designEloquent->name = $design->name;
        $designEloquent->date = $design->date;
        $designEloquent->last_edited = $currentDate;
        $designEloquent->document_file = $design->document_file;
        $designEloquent->project_id = $design->project_id;
        $designEloquent->design_work_id = $design->design_work_id;
        $designEloquent->uploader_id = $design->uploader_id;

        return $designEloquent;
    }
}