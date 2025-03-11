<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectRequirementEloquentModel;

class ProjectRequirementData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly mixed $document_file,
        public readonly int $project_id,
    ) {
    }

    public static function fromRequest(Request $request, ?int $requirement_id = null): ProjectRequirementData
    {
        return new self(
            id: $requirement_id,
            title: $request->string('title'),
            document_file: $request->document_file,
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(ProjectRequirementEloquentModel $prEloquent): self
    {
        return new self(
            id: $prEloquent->id,
            title: $prEloquent->title,
            document_file: $prEloquent->document_file,
            project_id: $prEloquent->project_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'document_file' => $this->document_file,
            'project_id' => $this->project_id
        ];
    }
}
