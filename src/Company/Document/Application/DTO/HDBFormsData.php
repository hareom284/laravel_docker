<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\HDBFormsEloquentModel;

class HDBFormsData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $date_uploaded,
        public readonly string $document_file,
        public readonly int $project_id
    )
    {}

    public static function fromRequest(Request $request, ?int $hdb_forms_id = null): HDBFormsData
    {
        return new self(
            id: $hdb_forms_id,
            name: $request->string('name'),
            date_uploaded: $request->string('date_uploaded'),
            document_file: $request->string('document_file'),
            project_id: $request->integer('project_id')
        );
    }

    public static function fromEloquent(HDBFormsEloquentModel $hdbFormsEloquent): self
    {
        $documentUrl = $hdbFormsEloquent->document_file ? asset('storage/hdb_forms_file/' . $hdbFormsEloquent->document_file) : null;

        return new self(
            id: $hdbFormsEloquent->id,
            name: $hdbFormsEloquent->name,
            date_uploaded: $hdbFormsEloquent->date_uploaded,
            document_file: $documentUrl,
            project_id: $hdbFormsEloquent->project_id,
        );
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date_uploaded' => $this->date_uploaded,
            'document_file' => $this->document_file,
            'project_id' => $this->project_id
        ];
    }
}