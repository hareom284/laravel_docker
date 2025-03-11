<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\FolderData;
use Src\Company\Document\Application\Mappers\FolderMapper;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Domain\Resources\FolderResource;
use Src\Company\Document\Domain\Repositories\FolderRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\FolderEloquentModel;

class FolderRepository implements FolderRepositoryInterface
{

    public function getFolders($filters = [])
    {
        //folder list
        $perPage = $filters['perPage'] ?? 10;

        $folderEloquent = FolderEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $folders = FolderResource::collection($folderEloquent);

        $links = [
            'first' => $folders->url(1),
            'last' => $folders->url($folders->lastPage()),
            'prev' => $folders->previousPageUrl(),
            'next' => $folders->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $folders->currentPage(),
            'from' => $folders->firstItem(),
            'last_page' => $folders->lastPage(),
            'path' => $folders->url($folders->currentPage()),
            'per_page' => $perPage,
            'to' => $folders->lastItem(),
            'total' => $folders->total(),
        ];
        $responseData['data'] = $folders;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $responseData;
    }

    public function getFoldersByProjectId(int $projectId)
    {
        $folderEloquent = FolderEloquentModel::query()->where('project_id',$projectId)->get();

        $folders = FolderResource::collection($folderEloquent);

        return $folders;

    }

    public function findFolderById(int $id)
    {
        $folderEloquent = FolderEloquentModel::query()->with('documents')->findOrFail($id);
        $folder = new FolderResource($folderEloquent);
        return $folder;
    }

    public function store(Folder $folder): FolderData
    {
        return DB::transaction(function () use ($folder) {

            $folderEloquent = FolderMapper::toEloquent($folder);

            $folderEloquent->save();

            return FolderData::fromEloquent($folderEloquent);
        });
    }
    public function update(Folder $folder): Folder
    {
        $folderArray = $folder->toArray();

        $folderEloquent = FolderEloquentModel::query()->findOrFail($folder->id);

        $folderEloquent->fill($folderArray);

        $folderEloquent->save();
        
        return $folder;
    }

    public function delete(int $folder_id): void
    {
        $folderEloquent = FolderEloquentModel::query()->findOrFail($folder_id);
        $folderEloquent->delete();
    }
}