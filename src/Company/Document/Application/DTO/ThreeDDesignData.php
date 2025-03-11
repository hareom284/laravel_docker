<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\ThreeDDesignEloquentModel;

class ThreeDDesignData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $name,
        public readonly ?string $date,
        public readonly ?string $last_edited,
        public readonly ?string $document_file,
        public readonly int $project_id,
        public readonly int $design_work_id,
        public readonly int $uploader_id
    )
    {}

    public static function fromRequest(Request $request, ?int $design_id = null): ThreeDDesignData
    {
        return new self(
            id: $design_id,
            name: $request->string('name'),
            date: $request->string('date'),
            last_edited: $request->string('last_edited'),
            document_file: $request->string('document_file'),
            project_id: $request->integer('project_id'),
            design_work_id: $request->integer('design_work_id'),
            uploader_id: $request->integer('uploader_id'),
        );
    }

    public static function fromEloquent(ThreeDDesignEloquentModel $designEloquent): self
    {
        return new self(
            id: $designEloquent->id,
            name: $designEloquent->name,
            date: $designEloquent->date,
            last_edited: $designEloquent->last_edited,
            document_file: $designEloquent->document_file,
            project_id: $designEloquent->project_id,
            design_work_id: $designEloquent->design_work_id,
            uploader_id: $designEloquent->uploader_id,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
            'last_edited' => $this->last_edited,
            'document_file' => $this->document_file,
            'project_id' => $this->project_id,
            'design_work_id' => $this->design_work_id,
            'uploader_id' => $this->uploader_id,
        ];
    }
}