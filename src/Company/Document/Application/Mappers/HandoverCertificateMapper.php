<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\PurchaseOrder;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Model\Entities\HandoverCertificate;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateEloquentModel;

class HandoverCertificateMapper
{
    public static function fromRequest(Request $request, ?int $id = null): HandoverCertificate
    {

        // Customer Signature
        if($request->file('customer_signature'))
        {
            $fileName =  'customer_'.time().'.'.$request->file('customer_signature')->extension();

            $filePath = 'handover/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->file('customer_signature')));

            $customerSignature = $fileName;
        } else {
            $customerSignature = null;
        }

        // Saleperson Signature
        if($request->file('salesperson_signature'))
        {
            $fileName =  'saleperson_'.time().'.'.$request->file('salesperson_signature')->extension();

            $filePath = 'handover/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->file('salesperson_signature')));

            $salepersonSignature = $fileName;
        } else {
            $salepersonSignature = null;
        }

        // Manager Signature
        if($request->file('manager_signature'))
        {
            $fileName =  'manager_'.time().'.'.$request->file('manager_signature')->extension();

            $filePath = 'handover/' . $fileName;
        
            Storage::disk('public')->put($filePath, file_get_contents($request->file('manager_signature')));

            $mangerSignature = $fileName;
        } else {
            $mangerSignature = null;
        }

        //get authenticated salesperson id (staff_id)
        $salespersonId = auth('sanctum')->user()->staffs->id;

        return new HandoverCertificate(
            id: $id,
            project_id: $request->integer('project_id'),
            signed_by_manager_id: $request->integer('signed_by_manager_id') ? $request->integer('signed_by_manager_id') : NULL,
            date: $request->string('date'),
            last_edited: $request->string('last_edited'),
            customer_signature: $customerSignature,
            salesperson_signature: $salepersonSignature,
            signed_by_salesperson_id: $salespersonId,
            manager_signature: $mangerSignature,
            status: $request->integer('status'),
        );
    }

    public static function fromEloquent(HandoverCertificateEloquentModel $handEloquent): HandoverCertificate
    {
        return new HandoverCertificate(
            id: $handEloquent->id,
            project_id: $handEloquent->project_id,
            signed_by_manager_id: $handEloquent->signed_by_manager_id,
            date: $handEloquent->date,
            last_edited: $handEloquent->last_edited,
            customer_signature: $handEloquent->customer_signature,
            salesperson_signature: $handEloquent->salesperson_signature,
            signed_by_salesperson_id: $handEloquent->signed_by_salesperson_id,
            manager_signature: $handEloquent->manager_signature,
            status: $handEloquent->status,
        );
    }

    public static function toEloquent(HandoverCertificate $handover): HandoverCertificateEloquentModel
    {
        $handEloquent = new HandoverCertificateEloquentModel();
        if ($handover->id) {
            $handEloquent = HandoverCertificateEloquentModel::query()->findOrFail($handover->id);
        }

        $handEloquent->project_id = $handover->project_id;
        $handEloquent->signed_by_manager_id = $handover->signed_by_manager_id;
        $handEloquent->date = $handover->date;
        $handEloquent->last_edited = $handover->last_edited;
        $handEloquent->customer_signature = $handover->customer_signature;
        $handEloquent->salesperson_signature = $handover->salesperson_signature;
        $handEloquent->signed_by_salesperson_id = $handover->signed_by_salesperson_id;
        $handEloquent->manager_signature = $handover->manager_signature;
        $handEloquent->status = $handover->status;
        return $handEloquent;
    }
}