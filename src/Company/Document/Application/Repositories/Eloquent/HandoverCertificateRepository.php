<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Domain\Resources\PurchaseOrderResource;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Application\DTO\HandoverCertificateData;
use Src\Company\Document\Application\Mappers\HandoverCertificateMapper;
use Src\Company\Document\Domain\Model\Entities\HandoverCertificate;
use Src\Company\Document\Domain\Repositories\HandoverCertificateRepositoryInterface;
use Src\Company\Document\Domain\Resources\HandoverCertificateDetailResource;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class HandoverCertificateRepository implements HandoverCertificateRepositoryInterface
{

    public function index()
    {
        $handEloquent = HandoverCertificateEloquentModel::query()->get();

        return $handEloquent;
    }

    public function getHandoverByProjectId(int $projectId)
    {
        $handEloquent = HandoverCertificateEloquentModel::query()->where('project_id', $projectId)->get();

        return $handEloquent;
    }

    public function getApproveHandoverCertificates()
    {
        $handEloquent = HandoverCertificateEloquentModel::query()->with('salesperson.user.roles', 'project.property', 'project.customer', 'project.saleReport')->orderBy('status', 'ASC')->get();

        return $handEloquent;
    }

    // public function store(HandoverCertificate $handoverData): HandoverCertificateData
    // {

    //     $handoverCertificateEloquent = HandoverCertificateMapper::toEloquent($handoverData);

    //     $handoverCertificateEloquent->save();

    //     return HandoverCertificateData::fromEloquent($handoverCertificateEloquent);
    // }

    public function salepersonSign($request)
    {
        // return $request->all();
        $saleperson_signature_file = '';

        if ($request->file('salesperson_signature')) {
            $saleFileName =  time() . '.' . $request->file('salesperson_signature')->extension();

            $saleFilePath = 'handover/saleperson/sign/' . $saleFileName;

            Storage::disk('public')->put($saleFilePath, file_get_contents($request->file('salesperson_signature')));

            $saleperson_signature_file = $saleFileName;
        }

        $staff = StaffEloquentModel::where('user_id', $request->saleperson_id)->first(['id']);

        $handover_certificate = HandoverCertificateEloquentModel::create([
            'project_id' => $request->project_id,
            'date' => $request->date,
            'last_edited' => $request->edited,
            'signed_by_salesperson_id' => $staff ? $staff->id : null,
            'status' => 1,
            'salesperson_signature' => $saleperson_signature_file,
            // 'customer_signature' => $initialFile
        ]);
        $customer_signature_file = null;

        // if($request->file('customer_signature'))
        // {
        // $customerFileName =  time().'.'.$request->file('customer_signature')->extension();

        // $customerFilePath = 'handover/customer/sign/' . $customerFileName;

        // Storage::disk('public')->put($customerFilePath, file_get_contents($request->file('customer_signature')));

        // $customer_signature_file = $customerFileName;
        // }
        $initialFile = null;
        if ($request->customer_signature) {
            $customer_signature_array = $request->customer_signature;
            if (count($customer_signature_array) > 0) {
                foreach ($customer_signature_array as $customer_sign) {

                    $timestamp = time();
                    $uniqueId = uniqid();
                    $extension = $customer_sign['customer_signature']->extension();
                    $customerFileName = "{$timestamp}_{$uniqueId}.{$extension}";
                    $customerFilePath = 'handover/customer/sign/' . $customerFileName;

                    Storage::disk('public')->put($customerFilePath, file_get_contents($customer_sign['customer_signature']));

                    $customer_signature_file = $customerFileName;


                    $signature_data = [
                        'handover_certificate_id' => $handover_certificate->id,
                        'customer_id' => $customer_sign['customer_id'],
                        'customer_signature' => $customer_signature_file
                    ];

                    $handover_certificate->customer_signatures()->create($signature_data);
                    $initialFile = $customer_signature_file;
                    if ($customer_signature_file) {
                        $customer = CustomerEloquentModel::query()->where('user_id', $customer_sign['customer_id'])->first();

                        $customer->update([
                            'status' => 3
                        ]);
                    }
                }
            }
        }
        if ($initialFile) {
            $projectEloquent = ProjectEloquentModel::query()->where('id', $request->project_id)->first();

            $projectEloquent->update([
                'project_status' => 'Completed'
            ]);
            $handover_certificate->update([
                'status' => $initialFile ? 2 : 1,
                'customer_signature' => $initialFile
            ]);
        }



        return $request;
    }

    public function getHandoverCertificateDetail(int $id)
    {
        $handEloquent = HandoverCertificateEloquentModel::query()
            ->with([
                'salesperson.user', 'salesperson.rank',
                'project.saleReport.customer_payments',
                'project.company',
                'project.property',
                'customer_signatures.customer.customers',
                'project.renovation_documents' => function ($query) {
                    $query->whereNotNull('signed_date');
                }
            ])
            ->where('project_id', $id)
            ->first();

        $handoverDetail = [];

        if ($handEloquent) {
            $handoverDetail  = new HandoverCertificateDetailResource($handEloquent);
        }

        return $handoverDetail;
    }

    public function managerSign($request)
    {
        // Manager Signature
        $fileName =  'manager_' . time() . '.' . $request->file('manager_signature')->extension();

        $filePath = 'handover/' . $fileName;

        Storage::disk('public')->put($filePath, file_get_contents($request->file('manager_signature')));

        $mangerSignature = $fileName;

        $user = auth('sanctum')->user();

        $handoverCertificateEloquent = HandoverCertificateEloquentModel::query()->findOrFail($request->id);

        $handoverCertificateEloquent->manager_signature = $mangerSignature;

        $handoverCertificateEloquent->signed_by_manager_id = $user->id;

        $handoverCertificateEloquent->status = 2;

        $handoverCertificateEloquent->save();

        return $handoverCertificateEloquent;
    }

    public function customerSign($request)
    {

        $customer_signature_file = '';

        $handoverCertificateEloquent = HandoverCertificateEloquentModel::query()->findOrFail($request->id);
        $handoverCertificateEloquent->customer_signature = "";
        $handoverCertificateEloquent->status = 1;
        $handoverCertificateEloquent->save();
        $customer_signature_file = null;
        $initialFile = null;
        if ($request->customer_signature) {
            $customer_signature_array = $request->customer_signature;
            if (count($customer_signature_array) > 0) {
                foreach ($customer_signature_array as $customer_sign) {

                    $timestamp = time();
                    $uniqueId = uniqid();
                    $extension = $customer_sign['customer_signature']->extension();
                    $customerFileName = "{$timestamp}_{$uniqueId}.{$extension}";
                    $customerFilePath = 'handover/customer/sign/' . $customerFileName;

                    Storage::disk('public')->put($customerFilePath, file_get_contents($customer_sign['customer_signature']));

                    $customer_signature_file = $customerFileName;


                    $signature_data = [
                        'handover_certificate_id' => $request->id,
                        'customer_id' => $customer_sign['customer_id'],
                        'customer_signature' => $customer_signature_file
                    ];

                    $handoverCertificateEloquent->customer_signatures()->create($signature_data);
                    $initialFile = $customer_signature_file;
                    if ($customer_signature_file) {
                        $customer = CustomerEloquentModel::query()->where('user_id', $customer_sign['customer_id'])->first();

                        $customer->update([
                            'status' => 3
                        ]);
                    }
                }
            }
        }
        if ($initialFile) {
            $projectEloquent = ProjectEloquentModel::query()->where('id', $handoverCertificateEloquent->project_id)->first();

            $projectEloquent->update([
                'project_status' => 'Completed'
            ]);
            $handoverCertificateEloquent->update([
                'status' => $initialFile ? 2 : 1,
                'customer_signature' => $initialFile
            ]);
        }

        return $handoverCertificateEloquent;
    }

    public function handoverSign($request)
    {
        $customer_signature_file = '';

        $handoverCertificateEloquent = HandoverCertificateEloquentModel::query()->findOrFail($request->id);
        $saleperson_signature_file = '';
        $isEnableProjectStatus = GeneralSettingEloquentModel::where('setting', "enable_change_project_status")
        ->where('value', "true")
        ->first();
        $isEnableAlternateHandoverFlow = GeneralSettingEloquentModel::where('setting', "enable_alternate_handover_flow")
        ->where('value', "true")
        ->first();
        $isEnableCommClaim = GeneralSettingEloquentModel::where('setting', "enable_comm_claim_flow")
        ->where('value', "true")
        ->first();

        if ($request->file('salesperson_signature')) {
            $saleFileName =  time() . '.' . $request->file('salesperson_signature')->extension();

            $saleFilePath = 'handover/saleperson/sign/' . $saleFileName;

            Storage::disk('public')->put($saleFilePath, file_get_contents($request->file('salesperson_signature')));

            $saleperson_signature_file = $saleFileName;
        }

        $staff = StaffEloquentModel::where('user_id', $request->saleperson_id)->first(['id']);

        if($saleperson_signature_file){
            $handover_certificate = $handoverCertificateEloquent->update([
                'project_id' => $request->project_id,
                'date' => $request->date,
                'last_edited' => $request->edited,
                'signed_by_salesperson_id' => $staff ? $staff->id : null,
                'status' => 1,
                'salesperson_signature' => $saleperson_signature_file,
                // 'customer_signature' => $initialFile
            ]);
        }
        $customer_signature_file = null;

        $initialFile = null;
        if ($request->customer_signature) {
            $customer_signature_array = $request->customer_signature;
            if (count($customer_signature_array) > 0) {
                foreach ($customer_signature_array as $customer_sign) {

                    $timestamp = time();
                    $uniqueId = uniqid();
                    $extension = $customer_sign['customer_signature']->extension();
                    $customerFileName = "{$timestamp}_{$uniqueId}.{$extension}";
                    $customerFilePath = 'handover/customer/sign/' . $customerFileName;

                    Storage::disk('public')->put($customerFilePath, file_get_contents($customer_sign['customer_signature']));

                    $customer_signature_file = $customerFileName;


                    $signature_data = [
                        'handover_certificate_id' => $handoverCertificateEloquent->id,
                        'customer_id' => $customer_sign['customer_id'],
                        'customer_signature' => $customer_signature_file
                    ];

                    $handoverCertificateEloquent->customer_signatures()->create($signature_data);
                    $initialFile = $customer_signature_file;
                    if ($customer_signature_file) {
                        $customer = CustomerEloquentModel::query()->where('user_id', $customer_sign['customer_id'])->first();
                        if(!$isEnableProjectStatus || !$isEnableCommClaim){
                            if(!$isEnableAlternateHandoverFlow){
                                $customer->update([
                                    'status' => 3
                                ]);
                            }
                        }
                    }
                }
            }
        }

        if ($initialFile && $handoverCertificateEloquent->salesperson_signature) {
            $projectEloquent = ProjectEloquentModel::query()->where('id', $request->project_id)->first();
            if($isEnableProjectStatus || $isEnableCommClaim){
                if(!$isEnableAlternateHandoverFlow){
                    $projectEloquent->update([
                        'project_status' => 'Claimed comm'
                    ]);
                }
            }else{
                if(!$isEnableAlternateHandoverFlow){
                    $projectEloquent->update([
                        'project_status' => 'Completed'
                    ]);
                }
            }
            $handoverCertificateEloquent->update([
                'status' => 2,
                'customer_signature' => $initialFile
            ]);
        }
        return $request;
    }
}
