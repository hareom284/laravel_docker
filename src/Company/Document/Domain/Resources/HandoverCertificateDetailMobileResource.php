<?php

namespace Src\Company\Document\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class HandoverCertificateDetailMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // $sale_signature = $this->salesperson_signature ? asset('storage/handover/' . $this->salesperson_signature) : null;
        $sale_signature = null;
        if ($this->salesperson_signature) {
            $sale_signature_file_path = 'handover/saleperson/sign/' . $this->salesperson_signature;

            $sale_signature_image = Storage::disk('public')->get($sale_signature_file_path);

            $sale_signature = base64_encode($sale_signature_image);
        }


        // $customer_signature = $this->customer_signature ? asset('storage/handover/' . $this->customer_signature) : null;
        $customer_signature = null;
        if ($this->customer_signature) {
            $customer_signature_file_path = 'handover/customer/sign/' . $this->customer_signature;

            $customer_signature_image = Storage::disk('public')->get($customer_signature_file_path);

            $customer_signature = base64_encode($customer_signature_image);
        }

        $manager_signature = null;
        $manager_name = '';
        if ($this->manager_signature) {
            $manager_signature_file_path = 'handover/' . $this->manager_signature;

            $manager_signature_image = Storage::disk('public')->get($manager_signature_file_path);

            $manager_signature = base64_encode($manager_signature_image);

            $manager_name = UserEloquentModel::find($this->signed_by_manager_id);
        }

        $documentHeaderFooter = DocumentStandardEloquentModel::where('name', 'tax invoice')->where('company_id', $this->project->company_id)->first(['header_text', 'footer_text']);
        $customer_base64Image = [];
        if (isset($this->customer_signatures) && count($this->customer_signatures) > 0) {
            foreach ($this->customer_signatures as $customer_sign) {
                $customer_file_path = 'handover/customer/sign/' . $customer_sign->customer_signature;

                $customer_image = Storage::disk('public')->get($customer_file_path);


                array_push($customer_base64Image, [
                    'customer' => $customer_sign->customer,
                    'customer_signature' => base64_encode($customer_image),
                    'customer_signature_url' => $customer_sign->customer_signature ? asset('storage/handover/customer/sign/' . $customer_sign->customer_signature): null
                ]);
            }
        } else if ($customer_signature) {
            array_push($customer_base64Image, [
                'customer' => $this->project->customer,
                'customer_signature' => $customer_signature
            ]);
        }
        return [
            'id' => $this->id,
            // 'ref_no' => $this->ref_no,
            'project_id' => $this->project->id,
            'status' => $this->status,
            'agreement_no' => $this->project->agreement_no,
            'date' => $this->date ? $this->date : $this->created_at,
            'reg_no' => $this->project->company->reg_no,
            'gst_reg_no' => $this->project->company->gst_reg_no,
            'hdb_license_no' => $this->project->company->hdb_license_no,
            'agreement_amount' => $this->project->renovation_documents,
            'progressive_payments' => $this->project?->saleReport?->customer_payments,
            'customer' => $this->project->customer->name_prefix . ' ' . $this->project->customer->first_name . ' ' . $this->project->customer->last_name,
            'customer_signatures' => $customer_base64Image,
            'address' => $this->project->property->block_num . ' ' . $this->project->property->street_name . ' ' . $this->project->property->unit_num,
            'spore' => $this->project->property->postal_code,
            'customer_mobile' => $this->project->customer->contact_no,
            // 'saleperson_signature' => $sale_signature,
            'saleperson_signature_url' => $this->salesperson_signature ? asset('storage/handover/saleperson/sign/' . $this->salesperson_signature): null,
            'customer_signature' => $customer_signature,
            'manager_signature' => $manager_signature,
            'manager_name' => $manager_name ? $manager_name->first_name . " " . $manager_name->last_name : null,
            'nric' => $this->project->customer->customers->nric,
            'full_address' => $this->project->property->block_num . ' ' . $this->project->property->street_name . ' ' . $this->project->property->unit_num . ' ' . $this->project->property->postal_code,
            'signed_saleperson' => $this->salesperson ? $this->salesperson->user->first_name . ' ' . $this->salesperson->user->last_name : '',
            'signed_sale_email' => $this->salesperson ? $this->salesperson->user->email : '',
            'signed_sale_ph' => $this->salesperson ? $this->salesperson->user->contact_no : '',
            'rank' => $this->salesperson ? $this->salesperson->rank->rank_name : '',
            'header_text' => $documentHeaderFooter ? $documentHeaderFooter->header_text : '',
            'footer_text' => $documentHeaderFooter ? $documentHeaderFooter->footer_text : ''
        ];
    }
}
