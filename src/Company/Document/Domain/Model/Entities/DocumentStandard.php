<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class DocumentStandard extends AggregateRoot implements \JsonSerializable
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}