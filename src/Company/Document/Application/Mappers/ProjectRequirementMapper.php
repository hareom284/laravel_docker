<?php

namespace Src\Company\Document\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectRequirementEloquentModel;
use Illuminate\Support\Facades\Storage;


class ProjectRequirementMapper
{
    public static function fromRequest(Request $request, ?int $requirement_id = null): ProjectRequirement
    {

        // if ($request->hasFile('document_file')) {

        //     $fileName =  time() . '.' . $request->document_file->getClientOriginalExtension();

        //     $filePath = 'project_requirement/' . $fileName;

        //     Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

        //     $documentFile = $fileName;
        // } else {
        //     // Set null if don't exists
        //     $documentFile = $request->original_document ? $request->original_document : null;
        // }

        return new ProjectRequirement(
            id: $requirement_id,
            title: $request->string('title'),
            document_file: $request->document_file,
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(ProjectRequirementEloquentModel $prEloquent): ProjectRequirement
    {
        return new ProjectRequirement(
            id: $prEloquent->id,
            title: $prEloquent->title,
            document_file: $prEloquent->document_file,
            project_id: $prEloquent->project_id
        );
    }

    public static function toEloquent(ProjectRequirement $requirement): ProjectRequirementEloquentModel
    {
        $prEloquent = new ProjectRequirementEloquentModel();
        if ($requirement->id) {
            $prEloquent = ProjectRequirementEloquentModel::query()->findOrFail($requirement->id);
        }
        $prEloquent->title = $requirement->title;
        $prEloquent->document_file = $requirement->document_file;
        $prEloquent->project_id = $requirement->project_id;
        return $prEloquent;
    }
}
