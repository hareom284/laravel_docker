<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\DTO\ProjectRequirementData;
use Src\Company\Document\Application\Mappers\ProjectRequirementMapper;
use Src\Company\Document\Domain\Model\Entities\ProjectRequirement;
use Src\Company\Document\Domain\Repositories\ProjectRequirementMobileRepositoryInterface;
use Src\Company\Document\Domain\Resources\ProjectRequirementMobileResource;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectRequirementEloquentModel;

class ProjectRequirementMobileRepository implements ProjectRequirementMobileRepositoryInterface
{

    public function getProjectRequirements($project_id)
    {
        //folder list

        $prEloquent = ProjectRequirementEloquentModel::query()->where('project_id', $project_id)->get();

        $requirements = ProjectRequirementMobileResource::collection($prEloquent);

        return $requirements;
    }

    public function findRequirementById(int $id)
    {
        $prEloquent = ProjectRequirementEloquentModel::query()->findOrFail($id);

        return (new ProjectRequirementMobileResource($prEloquent));
    }

    public function store(ProjectRequirement $requirement)
    {


        return DB::transaction(function () use ($requirement) {

            if ($requirement->document_file && count($requirement->document_file) > 0) {
                foreach ($requirement->document_file as $index => $file) {

                    $prEloquent = ProjectRequirementMapper::toEloquent($requirement);
                    $originalName = '';
                    if ($file) {
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $originalName = preg_replace("/[^a-zA-Z0-9\s]/", "", $originalName);
                        $originalName = substr($originalName, 0, 200); // Limiting the length to avoid very long filenames

                        // Generate a unique suffix to append
                        $timestamp = time();
                        $uniqueId = uniqid();
                        $extension = $file->getClientOriginalExtension();
                        $fileTitle = "{$originalName}_{$timestamp}_{$uniqueId}.{$extension}";

                        $filePath = 'project_requirement/' . $fileTitle;

                        Storage::disk('public')->put($filePath, file_get_contents($file));

                        $documentFile = $fileTitle;
                    } else {
                        $fileTitle = '';
                        $documentFile = $file->original_document ?? null;
                    }
                    $prEloquent->title = $originalName;
                    $prEloquent->document_file = $documentFile;
                    $prEloquent->save();
                }
            }

            // return $prEloquent;
            return ProjectRequirementData::fromEloquent($prEloquent);
        });
    }
    public function update($projectRequirement, $request)
    {

        $requirementEloquent = ProjectRequirementMapper::toEloquent($projectRequirement);

        $fileTitle = '';
        if ($request->hasFile('document_file')) {

            if ($request->original_document && Storage::disk('public')->exists('project_requirement/' . $request->original_document)) {
                Storage::disk('public')->delete('project_requirement/' . $request->original_document);
            }
            $originalName = pathinfo($request->document_file->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = preg_replace("/[^a-zA-Z0-9\s]/", "", $originalName);
            $originalName = substr($originalName, 0, 200); // Limiting the length to avoid very long filenames

            // Generate a unique suffix to append
            $timestamp = time();
            $uniqueId = uniqid();
            $extension = $request->document_file->getClientOriginalExtension();
            $fileTitle = "{$originalName}_{$timestamp}_{$uniqueId}.{$extension}";

            $filePath = 'project_requirement/' . $fileTitle;

            Storage::disk('public')->put($filePath, file_get_contents($request->document_file));

            $documentFile = $fileTitle;
            $fileTitle = $originalName;
        } else {
            // Set null if don't exists
            $documentFile = $request->original_document ? $request->original_document : null;
            $fileTitle = $request->title;
        }

        $requirementEloquent->document_file = $documentFile;
        $requirementEloquent->title = $fileTitle;
        $requirementEloquent->project_id = $request->project_id;
        $requirementEloquent->update();

        return $requirementEloquent;
    }

    public function delete(int $requirement_id): void
    {
        $prEloquent = ProjectRequirementEloquentModel::query()->findOrFail($requirement_id);
        if ($prEloquent->document_file && Storage::disk('public')->exists('project_requirement/' . $prEloquent->document_file)) {
            Storage::disk('public')->delete('project_requirement/' . $prEloquent->document_file);
        }
        $prEloquent->delete();
    }
}
