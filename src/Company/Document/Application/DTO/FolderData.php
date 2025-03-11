<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\FolderEloquentModel;

class FolderData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly bool $allow_customer_view,
        public readonly int $project_id,
    )
    {}

    public static function fromRequest(Request $request, ?int $folder_id = null): FolderData
    {
        return new self(
            id: $folder_id,
            title: $request->string('title'),
            allow_customer_view: $request->boolean('allow_customer_view'),
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(FolderEloquentModel $folderEloquent): self
    {
        return new self(
            id: $folderEloquent->id,
            title: $folderEloquent->title,
            allow_customer_view: $folderEloquent->allow_customer_view,
            project_id: $folderEloquent->project_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'allow_customer_view' => $this->allow_customer_view,
            'project_id' => $this->project_id
        ];
    }
}