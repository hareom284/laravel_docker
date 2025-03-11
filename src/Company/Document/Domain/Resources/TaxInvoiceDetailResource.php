<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class TaxInvoiceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $salePerson = UserEloquentModel::with('staffs')->where('id', $this->signed_by_saleperson_id)->first();

        $saleperson_signature = null;
        if($this->salesperson_signature)
        {
            $saleperson_signature_file_path = 'tax-invoice/saleperson/sign/' . $this->salesperson_signature;

            $saleperson_signature_image = Storage::disk('public')->get($saleperson_signature_file_path);

            $saleperson_signature = base64_encode($saleperson_signature_image);
        }

        if($this->signed_by_manager_id)
        {
            $manager = UserEloquentModel::where('id', $this->signed_by_manager_id)->first(['first_name', 'last_name','contact_no']);

            $manager_name = $manager->first_name . ' ' . $manager->last_name;

            $manager_contact_no = $manager->contact_no;

            $manager_signature = null;
            if($this->manager_signature)
            {
                $manager_signature_file_path = 'tax-invoice/manager/sign/' . $this->manager_signature;

                $manager_signature_image = Storage::disk('public')->get($manager_signature_file_path);

                $manager_signature = base64_encode($manager_signature_image);
            }
        }

        $taxStatus = '';
        switch ($this->status) {
            case '1':
                $taxStatus = "Pending Approval";
                break;

            case '2':
                $taxStatus = "Approved";
                break;

            case '3':
                $taxStatus = "Handover";
                break;

            default:
                $taxStatus = "Pending";
                break;
        }

        $documentStandard = DocumentStandardEloquentModel::where([
            ['company_id', $this->project->company_id],
            ['name', 'tax invoice']
        ])->first(['header_text', 'footer_text']);

        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'saleperson_name' => $salePerson ? $salePerson->first_name . " " . $salePerson->last_name : '',
            'saleperson_contact_no' => $salePerson ? $salePerson->contact_no : '',
            'saleperson_rank' => $salePerson->staffs->rank->rank_name,
            'status' => $taxStatus,
            'manager_name' => $this->signed_by_manager_id ? $manager_name : '', 
            'manager_contact_no' => $this->signed_by_manager_id ? $manager_contact_no : '',
            'header_text' => $documentStandard ? $documentStandard->header_text : '',
            'footer_text' => $documentStandard ? $documentStandard->footer_text : '',
            'saleperson_signature' => $saleperson_signature,
            'saleperson_signature_url' => $this->salesperson_signature ? asset('storage/tax-invoice/saleperson/sign/' . $this->salesperson_signature): null,
            'manager_signature_url' => isset($this->signed_by_manager_id) ? asset('storage/tax-invoice/manager/sign/'. $this->manager_signature) : null,
            'manager_signature' => $this->signed_by_manager_id ? $manager_signature : null,
            'project' => $this->project,
            'salesperson' => $this->salesperson
        ];
    }
}

