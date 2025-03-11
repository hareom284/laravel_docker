<?php

namespace Src\Company\Document\Application\DTO;

class RenovationDocumentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $type,
        public readonly string $version_number,
        public readonly string $disclaimer,
        public readonly string $special_discount_percentage,
        public readonly float $total_amount,
        public readonly string $salesperson_signature,
        public readonly ?int $signed_by_salesperson_id,
        public readonly string $customer_signature,
        public readonly string $additional_notes,
        public readonly int $project_id,
        public readonly int $document_standard_id,
        public readonly ?string $payment_terms,
        public readonly ?string $remark
    )
    {}
}