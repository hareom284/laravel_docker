<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class RenovationDocuments extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $type,
        public readonly string $version_number,
        public readonly string $disclaimer,
        public readonly string $special_discount_percentage,
        public readonly float $total_amount,
        public readonly ?string $salesperson_signature,
        public readonly ?int $signed_by_salesperson_id,
        public readonly string $customer_signature,
        public readonly string $additional_notes,
        public readonly ?int $project_id,
        public readonly ?int $document_standard_id,
        public readonly ?bool $ismerged,
        public readonly ?string $payment_terms,
        public readonly ?string $agreement_number,
        public readonly ?string $remark

    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'version_number' => $this->version_number,
            'disclaimer' => $this->disclaimer,
            'special_discount_percentage' => $this->special_discount_percentage,
            'total_amount' => $this->total_amount,
            'additional_notes' => $this->additional_notes,
            'salesperson_signature' => $this->salesperson_signature,
            'signed_by_salesperson_id' => $this->signed_by_salesperson_id,
            'customer_signature' => $this->customer_signature,
            'ismerged' => $this->ismerged,
            'payment_terms' => $this->payment_terms,
            'agreement_number' => $this->agreement_number,
            'remark' => $this->remark
        ];
    }
}
