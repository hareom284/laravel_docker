<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;

class DocumentStandardData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $header_text,
        public readonly ?string $footer_text,
        public readonly ?string $disclaimer,
        public readonly ?string $terms_and_conditions,
        public readonly int $company_id,
        public readonly ?string $payment_terms,
    )
    {}

    public static function fromRequest(Request $request, ?int $document_standard_id = null): DocumentStandardData
    {
        return new self(
            id: $document_standard_id,
            name: $request->string('name'),
            header_text: $request->string('header_text'),
            footer_text: $request->string('footer_text'),
            disclaimer: $request->string('disclaimer'),
            terms_and_conditions: $request->string('terms_and_conditions'),
            company_id: $request->integer('company_id'),
            payment_terms: $request->string('payment_terms')
        );
    }

    public static function fromEloquent(DocumentStandardEloquentModel $documentStandardEloquent): self
    {
        return new self(
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'header_text' => $this->header_text,
            'footer_text' => $this->footer_text,
            'disclaimer' => $this->disclaimer,
            'terms_and_conditions' => $this->terms_and_conditions,
            'company_id' => $this->company_id,
            'payment_terms' => $this->payment_terms
        ];
    }
}