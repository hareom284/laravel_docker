<?php

namespace Src\Company\Project\Domain\Resources;

use stdClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class ProjectDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $signQuotationStatus = false;
        $signedDate = null;
        foreach ($this->renovation_documents as $document) {
            if ($document->type === 'QUOTATION' && $document->signed_date !== null) {
                $signQuotationStatus = true;
                $signedDate = $document?->signed_date;
            }
        }

        $dueDate = date("d M Y", strtotime($this->expected_date_of_completion));

        $createDate = date("d M Y", strtotime($this->updated_at));
        $isJunior = false;

        $saleperson = [];
        $referral_commission = GeneralSettingEloquentModel::where('setting', 'referral_commission')->first();
        $company_earning_percentage = GeneralSettingEloquentModel::where('setting', 'company_earning_percentage')->first();
        $commission_hold_back_percentage = GeneralSettingEloquentModel::where('setting', 'commission_holdback')->first();
        // $over_ride_percent = GeneralSettingEloquentModel::where('setting', 'over_ride_percentage')->first();
        $commission_from_multiple_sales = GeneralSettingEloquentModel::where('setting', 'commission_from_multiple_sales')->first();
        $commission_from_multiple_sales_value = $commission_from_multiple_sales ? (float) $commission_from_multiple_sales->value : 0;
        $totalCommissionBase = $referral_commission ? $referral_commission->value : 0;
        $salespersonCount = count($this->salespersons);
        $over_ride_percent = $this->salespersons[0]?->staffs?->rank?->or_percent ?? 0;
        if ($salespersonCount > 1) {
            // Calculate total rank percentage for all salespersons if there are more than 1
            $totalRankPercent = 0;
            foreach ($this->salespersons as $salesperson) {
                $totalRankPercent += $salesperson->staffs->rank->commission_percent;
            }
        }
        foreach ($this->salespersons as $salesperson) {
            $salesperson_image_url = '';
            $obj = new stdClass();
            $obj->id = $salesperson->id;
            $obj->first_name = $salesperson->first_name;
            $obj->last_name = $salesperson->last_name;
            $obj->name_prefix = $salesperson->name_prefix;
            $obj->email = $salesperson->email;
            $obj->contact_no = $salesperson->contact_no;
            $obj->rank_name = $salesperson->staffs->rank->rank_name;
            $obj->roles = $salesperson->roles;

            $juniorRank = RankEloquentModel::orderBy('commission_percent', 'asc')->first();
            // $orCommission = $over_ride_percent ? (float) $over_ride_percent->value : 0;
            // $totalCommissionBase = 100;
            if ($salespersonCount == 1) {
                // $obj->commission = $totalCommissionBase;
                $obj->commission = round(intval($salesperson->staffs->rank->commission_percent));
                $isJunior = $salesperson->staffs->rank->id == $juniorRank->id;
            } else {
                // $commissionPercent = $salesperson->staffs->rank->commission_percent;

                // if ($totalRankPercent > 0) {
                //     $obj->commission = round(($commissionPercent / $totalRankPercent) * $totalCommissionBase);
                // } else {
                //     $obj->commission = round($totalCommissionBase / $salespersonCount);
                // }
                $deductionFactor = $commission_from_multiple_sales_value / 100;

                $commissionPercent = $salesperson->staffs->rank->commission_percent;
            
                $obj->commission = round($commissionPercent  * (1 - $deductionFactor), 2);

            }
                // $obj->commission = round(intval($salesperson->staffs->rank->commission_percent));

            if ($salesperson->profile_pic) {

                //sending image url instead of base64 because of localStorage limitation issue
                $saleProfileFilePath = 'storage/profile_pic/' . $salesperson->profile_pic;

                $salesperson_image_url = asset($saleProfileFilePath);

                // $saleperson_image = Storage::disk('public')->get($saleProfileFilePath);

                // $saleperson_base64Image = base64_encode($saleperson_image);
            }
            $obj->saleProfile = $salesperson_image_url;

            array_push($saleperson, $obj);
        }

        $companies = new stdClass();
        $companies->id = $this->company->id;
        $companies->name = $this->company->name;
        $companies->document_standard = $this->company?->document_standards ?? null;
        $companies->email = $this->company->email;
        $companies->hdb_license_no =  $this->company->hdb_license_no;
        $companies->reg_no = $this->company->reg_no;
        $companies->gst_reg_no = $this->company->gst_reg_no;
        $companies->gst_percentage = $this->company->gst_reg_no ? $this->company->gst : 0;
        $companies->main_office = $this->company->main_office;
        $company_base64Image = '';
        if ($this->company->logo) {
            $customer_file_path = 'logo/' . $this->company->logo;

            $company_image = Storage::disk('public')->get($customer_file_path);

            $company_base64Image = base64_encode($company_image);
        }
        $companies->company_logo = $company_base64Image;
        $companies->company_url = $this->company?->logo ? asset('storage/logo/' . $this->company->logo) : null;

        $customers = new stdClass();
        $customers->id = $this->customers->id;
        $customers->name = $this->customers->name_prefix . ' ' . $this->customers->first_name . ' ' . $this->customers->last_name;
        $customers->email = $this->customers->email;
        $customers->contact_no = $this->customers->contact_no;
        $customers->nric = $this->customers->customers->nric ?? "";
        $customers->company_name = $this->customers->customers->company_name ?? "";
        $customers->customer_type = $this->customers->customers->customer_type ?? "";
        $customers->profile_pic = $this->customers->profile_pic ? asset('storage/profile_pic/' . $this->customers->profile_pic) : null;

        $customers_array = $this->customersPivot->toArray();

        $properties = new stdClass();
        $properties->id = $this->properties->id;
        $properties->street_name = $this->properties->street_name;
        $properties->unit_num = $this->properties->unit_num;
        $properties->block_num = $this->properties->block_num;
        $properties->postal_code = $this->properties->postal_code;
        $properties->type_id = $this->properties->propertyType->id;
        $properties->type = $this->properties->propertyType->type;
        $workSchedule = $this->getMedia('work_schedule_document');
        $workScheduleCount = $workSchedule->count();
        $supplierInvoice = $this->getMedia('supplier_invoice_document');
        $supplierInvoiceCount = $supplierInvoice->count();

        return [
            'id' => $this->id,
            'agreement_no' => $this->agreement_no,
            'description' => $this->description,
            'invoice_no' => $this->invoice_no,
            'collection_of_keys' => $this->collection_of_keys,
            'expected_date_of_completion' => $this->expected_date_of_completion,
            'created_at' => $signedDate ? $signedDate : $this->updated_at,
            'createDate' => $createDate,
            'completed_date' => $this->completed_date ?? "-",
            'properties' => $properties,
            'project_status' => $this->project_status,
            'salesperson' => $saleperson,
            'companies' => $companies,
            'customers' => $customers,
            'customers_array' => $customers_array,
            'project_requirements' => $this->projectRequirements,
            'renovation_documents' => $this->renovation_documents,
            'contract' => $this->contract,
            'quotaiton_sign_status' => $signQuotationStatus,
            'project_portfolio' => $this->projectPortFolios,
            'due_date' => $dueDate,
            'design_work_count' => count($this->designWorks),
            'hdb_form_count' => count($this->hdbForms),
            'handover_count' => count($this->handoverCertificates),
            'tax_invoices_count' => count($this->taxInvoices),
            'three_ddesign_count' => count($this->threeDDesigns),
            'purchase_order_count' => isset($this->purchaseOrders) ? count($this->purchaseOrders) : 0,
            'work_schedule_count' => $workScheduleCount,
            'supplier_invoice_count' => $supplierInvoiceCount,
            'freezed' => $this->freezed,
            // 'purchase_order_count' => count($this->purchaseOrders),
            'user_id' => $this->contactUser->user_id ?? null,
            'request_note' => $this->request_note,
            'payment_status' => $this->payment_status,
            'is_junior' => $isJunior,
            'company_earning_percentage' => $company_earning_percentage ? (float) $company_earning_percentage->value : 0,
            'over_ride_percentage' => $over_ride_percent,
            'commission_hold_back_percentage' => $commission_hold_back_percentage ? (float) $commission_hold_back_percentage->value : 0,
            'term_and_condition_id' => $this->term_and_condition_id
        ];
    }
}
