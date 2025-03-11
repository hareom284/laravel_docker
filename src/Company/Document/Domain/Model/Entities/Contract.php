<?php

namespace Src\Company\Document\Domain\Model\Entities;

use Src\Common\Domain\Entity;

class Contract extends Entity
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $date,
        public readonly ?string $contract_sum,
        public readonly ?string $contractor_payment,
        public readonly ?string $contractor_days,
        public readonly ?string $termination_days,
        public readonly ?string $owner_signature,
        public readonly ?string $contractor_signature,
        public readonly ?string $employer_witness_name,
        public readonly ?string $contractor_witness_name,
        public readonly ?int $project_id,
        public readonly ?string $name,
        public readonly ?string $nric,
        public readonly ?string $company,
        public readonly ?string $law,
        public readonly ?string $address,
        public readonly ?string $pdpa_authorization,
        public readonly ?bool $is_already_signed
    ) {}



    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'contract_sum' => $this->contract_sum,
            'contractor_payment' => $this->contractor_payment,
            'contractor_days' => $this->contractor_days,
            'termination_days' => $this->termination_days,
            'owner_signature' => $this->owner_signature,
            'contractor_signature' => $this->contractor_signature,
            'employer_witness_name' => $this->employer_witness_name,
            'contractor_witness_name' => $this->contractor_witness_name,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'nric' => $this->nric,
            'company' => $this->company,
            'law' => $this->law,
            'address' => $this->address,
            'pdpa_authorization' => $this->pdpa_authorization,
            'is_already_signed' => $this->is_already_signed
        ];
    }
}
