<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $documentUrl = isset($this->document_file) ? asset('storage/contract/' . $this->document_file) : null;

        if ($this->owner_signature) {

            $ownerSignature = asset('storage/contract/' . $this->owner_signature);

            $owner_signature_file_path = 'contract/' . $this->owner_signature;

            $owner_signature_image = Storage::disk('public')->get($owner_signature_file_path);

            $ownerSignatureEncode = base64_encode($owner_signature_image);
        } else {
            $ownerSignature = null;
            $ownerSignatureEncode = null;
        }

        if ($this->contractor_signature) {
            $contractorSignature = asset('storage/contract/' . $this->contractor_signature);

            $contractor_signature_file_path = 'contract/' . $this->contractor_signature;

            $contractor_signature_image = Storage::disk('public')->get($contractor_signature_file_path);

            $contractorSignatureEncode = base64_encode($contractor_signature_image);
        } else {
            $contractorSignature = null;
            $contractorSignatureEncode = null;
        }

        // $ownerSignature = $this->owner_signature ? asset('storage/contract/' . $this->owner_signature) : null;

        // $contractorSignature = $this->contractor_signature ? asset('storage/contract/' . $this->contractor_signature) : null;
        $pdpa_pdf_file = "";
        $contract_pdf_file = "";
        if ($this->pdpa_pdf_file) {
            $pdpa_pdf_file =  asset('storage/pdfs/' . $this->pdpa_pdf_file);
        }
        if ($this->contract_pdf_file) {
            $contract_pdf_file =  asset('storage/pdfs/' . $this->contract_pdf_file);
        }
        return [
            'id' => $this->id,
            'customer_name' => $this->name,
            'nric' => $this->nric,
            'law' => $this->law,
            'company_name' => $this->company,
            'address' => $this->address,
            'created_date' => $this->created_at,
            'date' => $this->date,
            'document_file' => $documentUrl,
            'footer_text' => $this->footer_text,
            'contract_sum' => $this->contract_sum,
            'contractor_payment' => $this->contractor_payment,
            'contractor_days' => $this->contractor_days,
            'termination_days' => $this->termination_days,
            'owner_signature' => $ownerSignature,
            'encode_owner_signature' => $ownerSignatureEncode,
            'contractor_signature' => $contractorSignature,
            'encode_contractor_signature' => $contractorSignatureEncode,
            'pdpa_authorization' => $this->pdpa_authorization,
            'stages_of_renovation' => $this->stages_of_renovation,
            'liability_period' => $this->liability_period,
            'employer_witness_name' => $this->employer_witness_name,
            'contractor_witness_name' => $this->contractor_witness_name,
            'project_id' => $this->project_id,
            'project' => $this->project,
            'customer' => $this->project->customers,
            'company' => $this->project->company,
            'pdpa_pdf_file' => $pdpa_pdf_file,
            'contract_pdf_file' => $contract_pdf_file,
            'term_and_condition_signatures' => $this->termAndConditionSignatures,
            'is_already_signed' => $this->is_already_signed
        ];
    }
}
