<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;

class DocumentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly ?string $file_type,
        public readonly string $document_file,
        public readonly bool $allow_customer_view,
        public readonly ?int $folder_id,
        public readonly int $project_id,
        public readonly ?string $date,
    )
    {}

    public static function fromRequest(Request $request, ?int $document_id = null): DocumentData
    {
        return new self(
            id: $document_id,
            title: $request->string('title'),
            file_type: $request->string('file_type'),
            document_file: $request->string('document_file'),
            allow_customer_view: $request->boolean('allow_customer_view'),
            folder_id: $request->integer('folder_id'),
            project_id: $request->integer('project_id'),
            date: $request->string('date')
        );
    }

    public static function fromEloquent(DocumentEloquentModel $documentEloquent): self
    {
        return new self(
            id: $documentEloquent->id,
            title: $documentEloquent->title,
            file_type: $documentEloquent->file_type,
            document_file: $documentEloquent->document_file,
            allow_customer_view: $documentEloquent->allow_customer_view,
            folder_id: $documentEloquent->folder_id,
            project_id: $documentEloquent->project_id,
            date: $documentEloquent->date
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'file_type' => $this->file_type,
            'document_file' => $this->document_file,
            'allow_customer_view' => $this->allow_customer_view,
            'folder_id' => $this->folder_id,
            'project_id' => $this->project_id,
            'date' => $this->date
        ];
    }
}