<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;
use Illuminate\Support\Facades\Storage;


class DocumentMapper
{
    public static function fromRequest(Request $request, ?int $document_id = null): Document
    {
        if ($request->hasFile('attachment_file')) {

            $fileName =  time() . '.' . $request->attachment_file->getClientOriginalExtension();

            $filePath = 'document_file/' . $fileName;

            Storage::disk('public')->put($filePath, file_get_contents($request->attachment_file));

            $documentFile = $fileName;

            $fileType = strtolower($request->attachment_file->getClientOriginalExtension());
            
        } else {

            $documentFile = $request->original_document ? $request->original_document : null;

            $fileType = $request->file_type ? strtolower($request->file_type) : null;
        }

        $folderId = $request->has('folder_id') ? $request->integer('folder_id') : null;

        return new Document(
            id: $document_id,
            title: $request->string('title'),
            file_type: $fileType,
            document_file: $documentFile,
            allow_customer_view: $request->boolean('allow_customer_view'),
            folder_id: $folderId,
            project_id: $request->integer('project_id'),
            date: Carbon::now()
        );
    }

    public static function fromEloquent(DocumentEloquentModel $documentEloquent): Document
    {
        return new Document(
            id: $documentEloquent->id,
            title: $documentEloquent->title,
            file_type: $documentEloquent->file_type,
            document_file: $documentEloquent->document_file,
            allow_customer_view: $documentEloquent->allow_customer_view,
            folder_id: $documentEloquent->folder_id,
            project_id: $documentEloquent->project_id,
            date: $documentEloquent->date,
        );
    }

    public static function toEloquent(Document $document): DocumentEloquentModel
    {
        $documentEloquent = new DocumentEloquentModel();
        if ($document->id) {
            $documentEloquent = DocumentEloquentModel::query()->findOrFail($document->id);
        }
        $documentEloquent->title = $document->title;
        $documentEloquent->file_type = $document->file_type;
        $documentEloquent->document_file = $document->document_file;
        $documentEloquent->allow_customer_view = $document->allow_customer_view;
        $documentEloquent->folder_id = $document->folder_id;
        $documentEloquent->project_id = $document->project_id;
        $documentEloquent->date = $document->date;
        return $documentEloquent;
    }
}
