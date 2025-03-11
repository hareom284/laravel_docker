<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\DocumentData;
use Src\Company\Document\Application\Mappers\DocumentMapper;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Document\Domain\Resources\DocumentResource;
use Src\Company\Document\Domain\Repositories\DocumentRepositoryInterface;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;

class DocumentRepository implements DocumentRepositoryInterface
{

    public function getDocuments($filters = [])
    {
        //folder list
        $perPage = $filters['perPage'] ?? 10;

        $documentEloquent = DocumentEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $documents = DocumentResource::collection($documentEloquent);

        $links = [
            'first' => $documents->url(1),
            'last' => $documents->url($documents->lastPage()),
            'prev' => $documents->previousPageUrl(),
            'next' => $documents->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $documents->currentPage(),
            'from' => $documents->firstItem(),
            'last_page' => $documents->lastPage(),
            'path' => $documents->url($documents->currentPage()),
            'per_page' => $perPage,
            'to' => $documents->lastItem(),
            'total' => $documents->total(),
        ];
        $responseData['data'] = $documents;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $responseData;
    }

    public function getDocumentByProjectId(int $projectId)
    {
        $documentEloquent = DocumentEloquentModel::query()->where('project_id',$projectId)->whereNull('folder_id')->get();
        $documents = DocumentResource::collection($documentEloquent);

        return $documents;
    }

    public function findDocumentById(int $id)
    {
        $documentEloquent = DocumentEloquentModel::query()->with('folder')->findOrFail($id);
        $document = new DocumentResource($documentEloquent);
        return $document;
    }

    public function store(Document $document): DocumentData
    {
        return DB::transaction(function () use ($document) {

            $documentEloquent = DocumentMapper::toEloquent($document);

            $documentEloquent->save();

            return DocumentData::fromEloquent($documentEloquent);
        });
    }
    public function update(Document $document): Document
    {
        $documentArray = $document->toArray();

        $documentEloquent = DocumentEloquentModel::query()->findOrFail($document->id);

        $documentEloquent->fill($documentArray);

        $documentEloquent->save();
        
        return $document;
    }

    public function delete(int $document_id): void
    {
        $documentEloquent = DocumentEloquentModel::query()->findOrFail($document_id);
        $documentEloquent->delete();
    }
}