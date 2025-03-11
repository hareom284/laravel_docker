<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Document\Domain\Repositories\WorkScheduleRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\ThreeDDesignEloquentModel;

class WorkSchedulRepository implements WorkScheduleRepositoryInterface
{

    public function getWorkSchedules(int $projectId)
    {
        $project = ProjectEloquentModel::find($projectId);
        return $project->getMedia('work_schedule_document');
    }

    public function show($id)
    {
        return Media::where('uuid', $id)->firstOrFail();
    }

    public function store($projectId, $documentFiles)
    {
        $project = ProjectEloquentModel::find($projectId);
        $uploadedFiles = [];
        foreach ($documentFiles as $documentFile) {
            $media = $project->addMedia($documentFile)
                             ->usingName($documentFile->getClientOriginalName())
                             ->toMediaCollection('work_schedule_document');
    
            $uploadedFiles[] = $media->getUrl();
        }
        return $uploadedFiles;
    }

    public function delete($id)
    {
        $media = Media::where('uuid', $id)->firstOrFail();
        $media->delete();
    }
}