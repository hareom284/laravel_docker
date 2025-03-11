<?php

namespace Src\Company\Document\Application\Mappers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Illuminate\Support\Facades\Log;

class ContractMapper
{
    public static function fromRequest(Request $request, ?int $contract_id = null): Contract
    {

        // Owner Signature
        if($request->file('owner_signature'))
        {
            $ownerSignature =  time().'.'.$request->file('owner_signature')->extension();

            $ownerSignaturePath = 'contract/' . $ownerSignature;
        
            Storage::disk('public')->put($ownerSignaturePath, file_get_contents($request->file('owner_signature')));

            $ownerSignatureFile = $ownerSignature;
        } else {
            $ownerSignatureFile = null;
        }     

        // Contractor Signature
        if($request->file('contractor_signature'))
        {
            $contractorSignature =  time().'.'.$request->file('contractor_signature')->extension();

            $contractorSignaturePath = 'contract/' . $contractorSignature;
        
            Storage::disk('public')->put($contractorSignaturePath, file_get_contents($request->file('contractor_signature')));

            $contractorSignatureFile = $contractorSignature;
        }
        else {
            $contractorSignatureFile = null;
        }        

        return new Contract(
            id: $contract_id,
            date: Carbon::now(),
            contract_sum: $request->string('contract_sum'),
            contractor_payment: $request->string('contractor_payment'),
            contractor_days: $request->integer('contractor_days'),
            termination_days: $request->integer('termination_days'),
            owner_signature: $ownerSignatureFile,
            contractor_signature: $contractorSignatureFile,
            employer_witness_name: $request->string('employer_witness_name'),
            contractor_witness_name: $request->string('contractor_witness_name'),
            project_id: $request->project_id,
            name: $request->name,
            nric: $request->nric,
            company: $request->company,
            law: $request->law,
            address: $request->address,
            pdpa_authorization: $request->pdpa_authorization,
            is_already_signed: $request->is_already_signed
        );
    }

    public static function fromEloquent(ContractEloquentModel $contractEloquent): Contract
    {
        return new Contract(
            id: $contractEloquent->id,
            date: $contractEloquent->date,
            contract_sum: $contractEloquent->contract_sum,
            contractor_payment: $contractEloquent->contractor_payment,
            contractor_days: $contractEloquent->contractor_days,
            termination_days: $contractEloquent->termination_days,
            owner_signature: $contractEloquent->owner_signature,
            contractor_signature: $contractEloquent->contractor_signature,
            employer_witness_name: $contractEloquent->employer_witness_name,
            contractor_witness_name: $contractEloquent->contractor_witness_name,
            project_id: $contractEloquent->project_id,
            name: $contractEloquent->name,
            nric: $contractEloquent->nric,
            company: $contractEloquent->company,
            law: $contractEloquent->law,
            address: $contractEloquent->address,
            pdpa_authorization: $contractEloquent->pdpa_authorization,
            is_already_signed: $contractEloquent->is_already_signed
        );
    }

    public static function toEloquent(Contract $contract): ContractEloquentModel
    {
        $contractEloquent = new ContractEloquentModel();
        if ($contract->id) {
            $contractEloquent = ContractEloquentModel::query()->findOrFail($contract->id);
        }
        $contractEloquent->date = $contract->date;
        $contractEloquent->contract_sum = $contract->contract_sum;
        $contractEloquent->contractor_payment = $contract->contractor_payment;
        $contractEloquent->contractor_days = $contract->contractor_days;
        $contractEloquent->termination_days = $contract->termination_days;
        $contractEloquent->owner_signature = $contract->owner_signature;
        $contractEloquent->contractor_signature = $contract->contractor_signature;
        $contractEloquent->employer_witness_name = $contract->employer_witness_name;
        $contractEloquent->contractor_witness_name = $contract->contractor_witness_name;
        $contractEloquent->project_id = $contract->project_id;
        $contractEloquent->name = $contract->name;
        $contractEloquent->nric = $contract->nric;
        $contractEloquent->company = $contract->company;
        $contractEloquent->law = $contract->law;
        $contractEloquent->address = $contract->address;
        $contractEloquent->pdpa_authorization = $contract->pdpa_authorization;
        $contractEloquent->is_already_signed = $contract->is_already_signed;
        return $contractEloquent;
    }
}