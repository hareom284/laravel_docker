<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\Document\Domain\Repositories\HandoverCertificateMobileRepositoryInterface;
use Src\Company\Document\Domain\Resources\HandoverCertificateDetailMobileResource;
use Src\Company\Document\Domain\Resources\HandoverCertificateMobileResource;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class HandoverCertificateMobileRepository implements HandoverCertificateMobileRepositoryInterface
{


    public function getHandoverByProjectId(int $projectId)
    {
        $handEloquent = HandoverCertificateEloquentModel::query()->where('project_id', $projectId)->get();
        $handoverList = HandoverCertificateMobileResource::collection($handEloquent);
        return $handoverList;
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
            ->find($id);

        $handoverDetail = [];

        if ($handEloquent) {
            $handoverDetail  = new HandoverCertificateDetailMobileResource($handEloquent);
        }

        return $handoverDetail;
    }

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
}
