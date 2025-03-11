<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\DocumentData;
use Src\Company\Document\Application\DTO\DocumentStandardData;
use Src\Company\Document\Application\Mappers\DocumentStandardMapper;
use Src\Company\Document\Domain\Model\Entities\DocumentStandard;
use Src\Company\Document\Domain\Repositories\DocumentStandardRepositoryInterface;
use Src\Company\Document\Domain\Resources\DocumentStandardResource;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;

class DocumentStandardRepository implements DocumentStandardRepositoryInterface
{

    public function getDocumentStandards($filters = [])
    {
        //folder list
        $perPage = $filters['perPage'] ?? 10;

        $documentStandardEloquent = DocumentStandardEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $documentStandard = DocumentStandardResource::collection($documentStandardEloquent);

        $links = [
            'first' => $documentStandard->url(1),
            'last' => $documentStandard->url($documentStandard->lastPage()),
            'prev' => $documentStandard->previousPageUrl(),
            'next' => $documentStandard->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $documentStandard->currentPage(),
            'from' => $documentStandard->firstItem(),
            'last_page' => $documentStandard->lastPage(),
            'path' => $documentStandard->url($documentStandard->currentPage()),
            'per_page' => $perPage,
            'to' => $documentStandard->lastItem(),
            'total' => $documentStandard->total(),
        ];
        $responseData['data'] = $documentStandard;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $responseData;
    }

    public function findDocumentStandardByCompanyId(int $id)
    {
        $documentStandardEloquent = DocumentStandardEloquentModel::query()->with('companies')->where('company_id', $id)->get();
        return $documentStandardEloquent;
    }

    public function findDocumentStandardById(int $id): DocumentStandardData
    {
        $documentStandardEloquent = DocumentStandardEloquentModel::query()->findOrFail($id);
        return DocumentStandardData::fromEloquent($documentStandardEloquent, true, true, true);
    }

    public function store(DocumentStandard $documentStandard): DocumentStandardData
    {
        return DB::transaction(function () use ($documentStandard) {

            $documentStandardEloquent = DocumentStandardMapper::toEloquent($documentStandard);

            $documentStandardEloquent->save();

            return DocumentStandardData::fromEloquent($documentStandardEloquent);
        });
    }
    public function update(DocumentStandard $documentStandard): DocumentStandard
    {
        $documentStandardArray = $documentStandard->toArray();

        $documentStandardEloquent = DocumentStandardEloquentModel::query()->findOrFail($documentStandard->id);

        $documentStandardEloquent->fill($documentStandardArray);

        $documentStandardEloquent->save();
        
        return $documentStandard;
    }

    public function delete(int $document_standard_id): void
    {
        $documentStandardEloquent = DocumentStandardEloquentModel::query()->findOrFail($document_standard_id);
        $documentStandardEloquent->delete();
    }

}