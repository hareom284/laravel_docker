<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\DocumentStandard;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;

class DocumentStandardMapper
{
    public static function fromRequest($request, ?int $document_standard_id = null): DocumentStandard
    {
        return new DocumentStandard(
            id: $document_standard_id,
            name: $request->name,
            header_text: $request->header_text ? $request->header_text : null,
            footer_text: $request->footer_text ? $request->footer_text : null,
            disclaimer: $request->disclaimer ? $request->disclaimer : null,
            terms_and_conditions: $request->terms_and_conditions ? $request->terms_and_conditions : null,
            company_id: $request->company_id,
            payment_terms: $request->payment_terms
        );
    }

    public static function fromEloquent(DocumentStandardEloquentModel $documentStandardEloquent): DocumentStandard
    {
        return new DocumentStandard(
            id: $documentStandardEloquent->id,
            name: $documentStandardEloquent->name,
            header_text: $documentStandardEloquent->header_text,
            footer_text: $documentStandardEloquent->footer_text,
            disclaimer: $documentStandardEloquent->disclaimer,
            terms_and_conditions: $documentStandardEloquent->terms_and_conditions,
            company_id: $documentStandardEloquent->company_id,
            payment_terms: $documentStandardEloquent->payment_terms
        );
    }

    public static function toEloquent(DocumentStandard $documentStandard): DocumentStandardEloquentModel
    {
        $documentStandardEloquent = new DocumentStandardEloquentModel();
        if ($documentStandard->id) {
            $documentStandardEloquent = DocumentStandardEloquentModel::query()->findOrFail($documentStandard->id);
        }
        $documentStandardEloquent->name = $documentStandard->name;
        $documentStandardEloquent->header_text = $documentStandard->header_text;
        $documentStandardEloquent->footer_text = $documentStandard->footer_text;
        $documentStandardEloquent->disclaimer = $documentStandard->disclaimer;
        $documentStandardEloquent->terms_and_conditions = $documentStandard->terms_and_conditions;
        $documentStandardEloquent->company_id = $documentStandard->company_id;
        $documentStandardEloquent->payment_terms = $documentStandard->payment_terms;
       
        return $documentStandardEloquent;
    }
}