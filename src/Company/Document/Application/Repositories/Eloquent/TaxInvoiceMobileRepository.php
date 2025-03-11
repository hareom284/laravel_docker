<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Domain\Repositories\TaxInvoiceMobileRepositoryInterface;
use Src\Company\Document\Domain\Resources\TaxInvoiceDetailResource;
use Src\Company\Document\Infrastructure\EloquentModels\TaxInvoiceEloquentModel;

class TaxInvoiceMobileRepository implements TaxInvoiceMobileRepositoryInterface
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
            'status' => 1 //1 is pending, 2 is approved, 3 is hand_over
        ]);

        return $request;
    }

    public function findTaxByProjectId($projectId)
    {
        $final_result = TaxInvoiceEloquentModel::where('project_id', $projectId)->get(['id', 'created_at','status']);
        return $final_result;

    }

    public function findTaxById($id)
    {
        $taxInvoice = TaxInvoiceEloquentModel::with('salesperson')->find($id);

        $final_result = new TaxInvoiceDetailResource($taxInvoice);

        return $final_result;
    }
}
