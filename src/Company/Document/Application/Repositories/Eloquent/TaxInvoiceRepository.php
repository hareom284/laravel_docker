<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Repositories\TaxInvoiceRepositoryInterface;
use Src\Company\Document\Domain\Resources\TaxInvoiceDetailResource;
use Src\Company\Document\Infrastructure\EloquentModels\TaxInvoiceEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class TaxInvoiceRepository implements TaxInvoiceRepositoryInterface
{
    public function signTaxBySale($request)
    {
        $saleperson_signature_file = '';
        
        if($request->file('salesperson_signature'))
        {
            $saleFileName =  time().'.'.$request->file('salesperson_signature')->extension();
    
            $saleFilePath = 'tax-invoice/saleperson/sign/' . $saleFileName;
    
            Storage::disk('public')->put($saleFilePath, file_get_contents($request->file('salesperson_signature')));
    
            $saleperson_signature_file = $saleFileName;
        }

        TaxInvoiceEloquentModel::create([
            'customer_id' => $request->customer_id,
            'project_id' => $request->project_id,
            'signed_by_saleperson_id' =>  $request->saleperson_id,
            'salesperson_signature' => $saleperson_signature_file,
            'date' => $request->date,
            'last_edited' => $request->last_edited,
            'status' => 0 //0 is new, 1 is pending, 2 is approved, 3 is hand_over
        ]);

        return $request;
    }

    public function getListByStatusOrder(array $filters)
    {

        $perPage = $filters['perPage'] ?? '';
        $salePerson = $filters['salePerson'] ?? 0;
        $filterText = $filters['filterText'] ?? '';

        $taxEloquent = TaxInvoiceEloquentModel::query()->with('salesperson.roles','project.property','project.customer','project.saleReport')
                        ->where('status', '!=', 0)
                        ->orderBy('status','ASC');

        if ($salePerson !== 0) {
            $taxEloquent->whereHas('project', function ($query) use ($salePerson) {
                $query->whereHas('salespersons', function ($query1) use ($salePerson) {
                    $query1->where('salesperson_id', $salePerson);
                });
            });
        }

        if (!empty($filterText)) {
            $taxEloquent->whereHas('project', function ($query) use ($filterText) {
                $query->whereHas('properties', function ($query) use ($filterText) {
                    $query->where('street_name', 'LIKE', "%{$filterText}%")
                        ->orWhere('block_num', 'LIKE', "%{$filterText}%")
                        ->orWhere('unit_num', 'LIKE', "%{$filterText}%")
                        ->orWhere('postal_code', 'LIKE', "%{$filterText}%");
                })
                ->orWhereHas('customers', function ($query) use ($filterText) {
                    $query->where('first_name', 'LIKE', "%{$filterText}%")
                        ->orWhere('last_name', 'LIKE', "%{$filterText}%");
                })
                ->orWhereHas('salespersons', function ($query) use ($filterText) {
                    $query->where('first_name', 'LIKE', "%{$filterText}%")
                        ->orWhere('last_name', 'LIKE', "%{$filterText}%");
                });
            });
        }

        $taxInvoices = $taxEloquent->paginate($perPage);
        $taxData =  TaxInvoiceDetailResource::collection($taxInvoices);

        $links = [
            'first' => $taxData->url(1),
            'last' => $taxData->url($taxData->lastPage()),
            'prev' => $taxData->previousPageUrl(),
            'next' => $taxData->nextPageUrl(),
        ];

        $meta = [
            'current_page' => $taxData->currentPage(),
            'from' => $taxData->firstItem(),
            'last_page' => $taxData->lastPage(),
            'path' => $taxData->url($taxData->currentPage()),
            'per_page' => $filters['perPage'],
            'to' => $taxData->lastItem(),
            'total' => $taxData->total(),
        ];

        $responseData['data'] = $taxData;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;

        return $responseData;
    }

    public function signTaxByManager($request)
    {
        $isEnableAlternateHandoverFlow = GeneralSettingEloquentModel::where('setting', "enable_alternate_handover_flow")
        ->where('value', "true")
        ->first();
        $isEnableProjectStatus = GeneralSettingEloquentModel::where('setting', "enable_change_project_status")
        ->where('value', "true")
        ->first();
        $isEnableCommClaim = GeneralSettingEloquentModel::where('setting', "enable_comm_claim_flow")
        ->where('value', "true")
        ->first();
        $manager_signature_file = '';

        if($request->file('manager_signature'))
        {
            $managerFileName =  time().'.'.$request->file('manager_signature')->extension();
    
            $managerFilePath = 'tax-invoice/manager/sign/' . $managerFileName;
    
            Storage::disk('public')->put($managerFilePath, file_get_contents($request->file('manager_signature')));
    
            $manager_signature_file = $managerFileName;

        }

        //    $taxInvoiceEloquent = TaxInvoiceEloquentModel::where('id', $request->tax_invoice_id)->update([
        //         'signed_by_manager_id' => $request->manager_id,
        //         'manager_signature' => $manager_signature_file,
        //         'status' => 2,
        //         'last_edited' => $request->last_edited
        //     ]);

        $taxInvoiceEloquent = TaxInvoiceEloquentModel::find($request->tax_invoice_id);

        if ($taxInvoiceEloquent) {
            $taxInvoiceEloquent->signed_by_manager_id = $request->manager_id;
            $taxInvoiceEloquent->manager_signature = $manager_signature_file;
            $taxInvoiceEloquent->status = 2;
            $taxInvoiceEloquent->last_edited = $request->last_edited;

            $taxInvoiceEloquent->save();

            if ($isEnableAlternateHandoverFlow) {
                if (!$isEnableProjectStatus || !$isEnableCommClaim) {
                    if ($taxInvoiceEloquent->project) {
                        $taxInvoiceEloquent->project->update([
                            'project_status' => 'Completed'
                        ]);

                        if ($taxInvoiceEloquent->project->customerUsers->isNotEmpty()) {
                            foreach ($taxInvoiceEloquent->project->customerUsers as $user) {
                                $user = UserEloquentModel::find($user->id);
                                if ($user && $user->customers) {
                                    $user->customers->update([
                                        'status' => 3
                                    ]);
                                }
                            }
                        }
                    }
                } else {
                    if ($taxInvoiceEloquent->project) {
                        $taxInvoiceEloquent->project->update([
                            'project_status' => 'Claimed comm'
                        ]);
                    }
                }
            }
        }

        return $request;

    }

    public function findTaxById($id)
    {
        $taxInvoice = TaxInvoiceEloquentModel::with('salesperson')->find($id);

        $final_result = new TaxInvoiceDetailResource($taxInvoice);

        return $final_result;
    }

    public function changeStatus($taxId, $status)
    {
        $taxInvoice = TaxInvoiceEloquentModel::find($taxId);
        if($taxInvoice){
            $taxInvoice->update([
                'status' => $status
            ]);
        }
        return $taxInvoice;
    }
}
