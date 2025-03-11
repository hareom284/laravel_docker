<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Src\Company\System\Domain\Model\Entities\GeneralSetting;
use Src\Company\System\Domain\Resources\GeneralSettingResource;
use Src\Company\System\Domain\Resources\LogoAndFaviconResource;
use Src\Company\System\Domain\Repositories\GeneralSettingRepositoryInterface;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Illuminate\Support\Facades\Log;

class GeneralSettingRepository implements GeneralSettingRepositoryInterface
{
    public function findByName($generalSetting)
    {
        $defaultValue = [
            'enable_sub_item' => 'false',
            'enable_only_show_discount_amount' => 'false',
            'enable_show_last_name_first' => 'false',
            'vendor_invoice_number_of_approval_required' => 1,
            'enable_payment_terms' => 'false',
            'enable_paid_customer_payment' => 'false',
            'enable_cn_in_vo' => 'false',
            'check_amend_cost_price' => 'false',
            'show_selected_items_below_aow' => 'false',
            'enable_multiple_term_and_conditions' => 'false',
            'enable_customer_sign_handover' => 'false',
            'enable_change_project_status' => 'false',
            'enable_referrer_form' => 'false',
            'enable_payment_terms_document_standard' => 'false',
            'show_supplier_invoice_work_schedule' => 'false',
            'enable_data_migration_mode' => 'false',
            'enable_page_break_option' => 'false',
            'enable_sub_description_feature' => 'false',
            'enable_hide_cost_price_switch' => 'false',
            'enable_document_remark' => 'false',
            'enable_po_from_section' => 'false',
            'show_document_section_left' => 'false',
            'enable_alternate_handover_flow' => 'false',
            'enable_contract_templates' => 'false',
            'enable_comm_claim_flow' => 'false',
            'use_predefined_aow_suggestions' => 'false',
            'download_unsign_term_condition_pdf' => 'false'
        ];

        $generalSettingEloquent = GeneralSettingEloquentModel::where('setting', $generalSetting)->first();
        if (!$generalSettingEloquent) {
            // If not, create a new instance with the default values
            $generalSettingEloquent = new GeneralSettingEloquentModel([
                'setting' => $generalSetting,
                'value' => $defaultValue[$generalSetting] ?? '',
                'is_array' => false
            ]);
        }

        return new GeneralSettingResource($generalSettingEloquent);
    }
    public function findAllGeneralSettings()
    {
        $generalSettings = GeneralSettingEloquentModel::all();
        return GeneralSettingResource::collection($generalSettings);
    }


    public function update(array $generals): array
    {

        $updatedGenerals = [];

        foreach ($generals as $general) {

            $setting = $general ? ($general->setting ?? $general['setting']) : null;
            $value = $general ? ($general->value ?? json_encode($general['value'])) : null;


            if (is_null($value) || $value === '') {
                $value = '';
            } elseif ($setting === 'whatsapp_access_token' && $value!='******************************************************') {
                $value = $this->isEncrypted($value) ? $value : Crypt::encryptString($value);
            } elseif($setting === 'whatsapp_access_token' && $value=='******************************************************') {
                $originalToken = GeneralSettingEloquentModel::where('setting','whatsapp_access_token')->first();
                if($originalToken){
                    $value = $originalToken->value;
                }
            }

            $generalEloquent = GeneralSettingEloquentModel::updateOrCreate(
                ['setting' => $setting], // Attributes to search for
                [
                    'setting' => $setting,
                    'value' => $value,
                    'is_array' => true
                ]
            );

            // Add the updated GeneralSettingEloquentModel instance to the array

            $updatedGenerals[] = $generalEloquent;
        }

        return $updatedGenerals;
    }

    public function isEncrypted($value)
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
