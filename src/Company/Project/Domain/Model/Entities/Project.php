<?php

namespace Src\Company\Project\Domain\Model\Entities;

use Src\Common\Domain\AggregateRoot;

class Project extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $invoice_no,
        public readonly string $description,
        public readonly string $collection_of_keys,
        public readonly string $expected_date_of_completion,
        // public readonly string $completed_date,
        public readonly string $project_status,
        public readonly string|int|null $customer_id,
        public readonly int $property_id,
        public readonly int $company_id,
        public readonly ?string $payment_status,
        public readonly ?string $request_note,
        public readonly ?int $term_and_condition_id
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_no' => $this->invoice_no,
            'description' => $this->description,
            'collection_kof_keys' => $this->collection_of_keys,
            'expected_date_of_completion' => $this->expected_date_of_completion,
            //    'completed_date' => $this->completed_date,
            'project_status' => $this->project_status,
            'customer_id' => $this->customer_id,
            'property_id' => $this->property_id,
            'company_id' => $this->company_id,
            'payment_status' => $this->payment_status,
            'request_note' => $this->request_note,
            'term_and_condition_id' => $this->term_and_condition_id
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
