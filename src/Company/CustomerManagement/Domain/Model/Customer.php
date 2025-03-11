<?php

namespace Src\Company\CustomerManagement\Domain\Model;

use Src\Common\Domain\AggregateRoot;

class Customer extends AggregateRoot implements \JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $nric,
        public readonly ?string $attachment,
        public readonly ?int $status,
        public readonly ?string $additional_information,
        public readonly ?int $assigned_by_management_id,
        public readonly ?int $user_id,
        public readonly ?int $last_modified_by,
        public readonly ?string $source,
        public readonly ?string $company_name,
        public readonly ?string $customer_type,
        public readonly ?string $budget,
        public readonly ?string $quote_value,
        public readonly ?string $book_value,
        public readonly ?string $key_collection,
        public readonly ?string $id_milestone_id,
        public readonly ?string $rejected_reason_id,
        public readonly ?string $next_meeting,
        public readonly ?string $days_aging,
        public readonly ?string $remarks,
        public readonly ?float $budget_value,
    ) {
    }



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nric' => $this->nric,
            'attachment' => $this->attachment,
            'status' => $this->status,
            'source' => $this->source,
            'additional_information' => $this->additional_information,
            'assigned_by_management_id' => $this->assigned_by_management_id,
            'user_id' => $this->user_id,
            'last_modified_by' => $this->last_modified_by,
            'company_name' => $this->company_name,
            'customer_type' => $this->customer_type,
            'budget' => $this->budget,
            'quote_value' => $this->quote_value,
            'book_value' => $this->book_value,
            'key_collection' => $this->key_collection,
            'id_milestone_id' => $this->id_milestone_id,
            'rejected_reason_id' => $this->rejected_reason_id,
            'next_meeting' => $this->next_meeting,
            'days_aging' => $this->days_aging,
            'remarks' => $this->remarks,
            'budget_value' => $this->budget_value
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
