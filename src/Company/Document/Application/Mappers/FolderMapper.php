<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\Folder;
use Src\Company\Document\Infrastructure\EloquentModels\FolderEloquentModel;

class FolderMapper
{
    public static function fromRequest(Request $request, ?int $folder_id = null): Folder
    {
        return new Folder(
            id: $folder_id,
            title: $request->string('title'),
            allow_customer_view: $request->boolean('allow_customer_view'),
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(FolderEloquentModel $folderEloquent): Folder
    {
        return new Folder(
            id: $folderEloquent->id,
            title: $folderEloquent->title,
            allow_customer_view: $folderEloquent->allow_customer_view,
            project_id: $folderEloquent->project_id
        );
    }

    public static function toEloquent(Folder $folder): FolderEloquentModel
    {
        $folderEloquent = new FolderEloquentModel();
        if ($folder->id) {
            $folderEloquent = FolderEloquentModel::query()->findOrFail($folder->id);
        }
        $folderEloquent->title = $folder->title;
        $folderEloquent->allow_customer_view = $folder->allow_customer_view;
        $folderEloquent->project_id = $folder->project_id;
        return $folderEloquent;
    }
}