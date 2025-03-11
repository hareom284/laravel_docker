<?php

namespace Src\Company\Document\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;

class ContractData
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $date,
        public readonly ?string $footer_text,
        public readonly ?string $document_file,
        public readonly ?string $contract_sum,
        public readonly ?string $contractor_payment,
        public readonly ?string $owner_signature,
        public readonly ?string $contractor_signature,
        public readonly ?string $stages_of_renovation,
        public readonly ?string $liability_period,
        public readonly ?string $employer_witness_name,
        public readonly ?string $contractor_witness_name,
        public readonly ?int $project_id,
        public readonly ?string $pdpa_authorization,
        public readonly ?bool $is_already_signed
    )
    {}

    public static function fromRequest(Request $request, ?int $document_id = null): ContractData
    {
        return new self(
            id: $document_id,
            date: $request->string('date'),
            footer_text: $request->string('footer_text'),
            document_file: $request->string('document_file'),
            contract_sum: $request->string('contract_sum'),
            contractor_payment: $request->string('contractor_payment'),
            owner_signature: $request->string('owner_signature'),
            contractor_signature: $request->string('contractor_signature'),
            stages_of_renovation: $request->string('stages_of_renovation'),
            liability_period: $request->string('liability_period'),
            employer_witness_name: $request->string('employer_witness_name'),
            contractor_witness_name: $request->string('contractor_witness_name'),
            project_id: $request->int('project_id'),
            pdpa_authorization: $request->string('pdpa_authorization'),
            is_already_signed: $request->boolean('is_already_signed')
        );
    }

    public static function fromEloquent(ContractEloquentModel $contract): self
    {
        return new self(
            id: $contract->id,
            date: $contract->date,
            footer_text: $contract->footer_text,
            document_file: $contract->document_file,
            contract_sum: $contract->contract_sum,
            contractor_payment: $contract->contractor_payment,
            owner_signature: $contract->owner_signature,
            contractor_signature: $contract->contractor_signature,
            stages_of_renovation: $contract->stages_of_renovation,
            liability_period: $contract->liability_period,
            employer_witness_name: $contract->employer_witness_name,
            contractor_witness_name: $contract->contractor_witness_name,
            project_id: $contract->project_id,
            pdpa_authorization: $contract->pdpa_authorization,
            is_already_signed: $contract->is_already_signed
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'footer_text' => $this->footer_text,
            'document_file' => $this->document_file,
            'contract_sum' => $this->contract_sum,
            'contractor_payment' => $this->contractor_payment,
            'owner_signature' => $this->owner_signature,
            'contractor_signature' => $this->contractor_signature,
            'stages_of_renovation' => $this->stages_of_renovation,
            'liability_period' => $this->liability_period,
            'employer_witness_name' => $this->employer_witness_name,
            'contractor_witness_name' => $this->contractor_witness_name,
            'project_id' => $this->project_id,
            'pdpa_authorization' => $this->pdpa_authorization,
            'is_already_signed' => $this->is_already_signed
        ];
    }
}