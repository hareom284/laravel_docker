<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Company\Document\Application\DTO\ContractData;
use Src\Company\Document\Application\DTO\DocumentData;
use Src\Company\Document\Application\Mappers\ContractMapper;
use Src\Company\Document\Application\Mappers\DocumentMapper;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Document\Domain\Repositories\ContractRepositoryInterface;
use Src\Company\Document\Domain\Resources\ContractResource;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Src\Company\Document\Domain\Mail\NotifyDocumentSignEmail;

class ContractRepository implements ContractRepositoryInterface
{

    public function getContract($projectId)
    {
        $contract_info = ContractEloquentModel::query()->where('project_id',$projectId)->first();

        $contract = new ContractResource($contract_info);
        
        return $contract;
    }

    public function getContractAmt($projectId)
    {
        $contract_info = ContractEloquentModel::query()->where('project_id',$projectId)->first();

        return $contract_info;
    }

    public function getContractById($contractId)
    {
        $contract_info = ContractEloquentModel::query()->with('project','project.company','project.customers', 'termAndConditionSignatures')->findOrFail($contractId);
        
        $contract = new ContractResource($contract_info);
        
        return $contract;
    }

    public function store(Contract $contract): Contract
    {
        $contractEloquent = ContractMapper::toEloquent($contract);
        
        $contractEloquent->save();

        return ContractMapper::fromEloquent($contractEloquent);

    }
    public function signContract(Request $request)
    {
        // Owner Signature
        if($request->file('owner_signature'))
        {
            $ownerSignature =  time().'_owner.'.$request->file('owner_signature')->extension();

            $ownerSignaturePath = 'contract/' . $ownerSignature;
        
            Storage::disk('public')->put($ownerSignaturePath, file_get_contents($request->file('owner_signature')));

            $ownerSignatureFile = $ownerSignature;
        } else {
            $ownerSignatureFile = null;
        }

        // Contractor Signature
        if($request->file('contractor_signature'))
        {
            $contractorSignature =  time().'_contractor.'.$request->file('contractor_signature')->extension();

            $contractorSignaturePath = 'contract/' . $contractorSignature;
        
            Storage::disk('public')->put($contractorSignaturePath, file_get_contents($request->file('contractor_signature')));

            $contractorSignatureFile = $contractorSignature;
        } else {
            $contractorSignatureFile = null;
        }

        $contractEloquent = ContractEloquentModel::query()->findOrFail($request->contract_id);

        // if contractor signature already existed in database, just assign db value
        if($contractEloquent->contractor_signature){
            $contractorSignatureFile = $contractEloquent->contractor_signature;
        }

        $contractEloquent->owner_signature = $ownerSignatureFile;

        $contractEloquent->contractor_signature = $contractorSignatureFile;

        $contractEloquent->employer_witness_name = $request->employer_witness_name;

        $contractEloquent->contractor_witness_name = $request->contractor_witness_name;

        $contractEloquent->save();

        $projectData = ProjectEloquentModel::query()->with('properties','salespersons','customer')->findOrFail($contractEloquent->project_id);

        $projectData->project_status = 'InProgress';

        $projectData->update();

        $mailgun = config('services.mailgun.secret');

        // fire email if mailgun exist in env
        if(isset($mailgun)){
            foreach ($projectData->salespersons as $saleperson) {

                $emailData = [
                    'address' => $projectData->properties->block_num.' '.$projectData->properties->street_name.' #'.$projectData->properties->unit_num.' Singapore '.$projectData->properties->postal_code,
                    'customer' => $projectData->customer->first_name.' '.$projectData->customer->last_name,
                    'type' => "Contract",
                    'saleperson' => $saleperson->first_name.' '.$saleperson->last_name,
                ];
    
                Mail::to($saleperson->email)->send(new NotifyDocumentSignEmail($emailData));
            }
        }

        // $contract = new ContractResource($contractEloquent);
        
        return $contractEloquent;
    }
    
    public function customerSign(Request $request)
    {
        // Owner Signature
        if($request->file('owner_signature'))
        {
            $ownerSignature =  time().'_owner.'.$request->file('owner_signature')->extension();

            $ownerSignaturePath = 'contract/' . $ownerSignature;
        
            Storage::disk('public')->put($ownerSignaturePath, file_get_contents($request->file('owner_signature')));

            $ownerSignatureFile = $ownerSignature;
            $employer_witness_name = $request->employer_witness_name;
        } else {
            $ownerSignatureFile = null;
            $employer_witness_name = null;
        }

        $contractEloquent = ContractEloquentModel::query()->findOrFail($request->contract_id);

        $contractEloquent->owner_signature = $ownerSignatureFile;

        $contractEloquent->employer_witness_name = $employer_witness_name;

        $contractEloquent->save();

        $projectData = ProjectEloquentModel::query()->findOrFail($contractEloquent->project_id);

        $projectData->project_status = 'InProgress';

        $projectData->update();
        
        return $contractEloquent;
    }
}