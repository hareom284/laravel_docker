<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Src\Company\System\Domain\Resources\GeneralSettingResource;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;
use Src\Company\System\Domain\Repositories\AccountingSettingRepositoryInterface;
use Src\Company\System\Domain\Resources\AccountingSettingResource;
use Src\Company\System\Infrastructure\EloquentModels\AccountingSettingEloquentModel;

class AccountingSettingRepository implements AccountingSettingRepositoryInterface
{
    public function findByCompany($companyId)
    {
        $accountingSettingEloquent = AccountingSettingEloquentModel::where('company_id', $companyId)->get();

        if($accountingSettingEloquent->isEmpty()){
            return [
                [
                    'setting' => 'taxCode',
                    'value' => '',
                    'company_id' => $companyId
                ],
                [
                    'setting' => 'taxRate',
                    'value' => '',
                    'company_id' => $companyId
                ],
                [
                    'setting' => 'InvoiceServices',
                    'value' => '',
                    'company_id' => $companyId
                ],
                [
                    'setting' => 'rebateCategory',
                    'value' => '',
                    'company_id' => $companyId
                ],
                [
                    'setting' => 'billTaxCalculation',
                    'value' => 'None',
                    'company_id' => $companyId
                ]
            ];
        }else{
            return AccountingSettingResource::collection($accountingSettingEloquent);
        }
    }

    public function update(array $settings)
    {
        foreach ($settings as $setting) {

            $value = $setting ? $setting['value'] : null;


            if (is_null($value) || $value === '') {
                $value = '';
            }

            $generalEloquent = AccountingSettingEloquentModel::updateOrCreate(
                ['company_id' => $setting['company_id'], 'setting' => $setting['setting']], // Attributes to search for
                [
                    'value' => $value,
                ]
            );
        }

        return true;   
    }
}
