<?php

namespace Src\Company\System\Application\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QuickBookEloquentModel;
use Src\Company\System\Application\DTO\CompanyData;
use Src\Company\System\Application\Mappers\CompanyMapper;
use Src\Company\System\Domain\Model\Entities\Company;
use Src\Company\System\Domain\Resources\CompanyResource;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class CompanyRepository implements CompanyRepositoryInterface
{

    public function getCompanies($filters = [])
    {
        //companies list
        $perPage = $filters['perPage'] ?? 10;

        $companyEloquent = CompanyEloquentModel::filter($filters)->orderBy('id', 'desc')->paginate($perPage);

        $companies = CompanyResource::collection($companyEloquent);

        $links = [
            'first' => $companies->url(1),
            'last' => $companies->url($companies->lastPage()),
            'prev' => $companies->previousPageUrl(),
            'next' => $companies->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $companies->currentPage(),
            'from' => $companies->firstItem(),
            'last_page' => $companies->lastPage(),
            'path' => $companies->url($companies->currentPage()),
            'per_page' => $perPage,
            'to' => $companies->lastItem(),
            'total' => $companies->total(),
        ];
        $responseData['data'] = $companies;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $responseData;
    }

    public function getAll()
    {
        $companies = CompanyEloquentModel::with('quickbookCredentials')
            ->select('id','name')
            ->get();

        return $companies;
    }

    public function findById(int $id)
    {
        $companyEloquent = CompanyEloquentModel::query()->findOrFail($id);

        $company = new CompanyResource($companyEloquent);

        return $company;
    }

    public function getDefaultCompany()
    {
        $companyEloquent = CompanyEloquentModel::query()->where('is_default',1)->first();

        $company = new CompanyResource($companyEloquent);

        return $company;
    }

    public function store(Company $company): CompanyData
    {
        // $documentStandardsData = [
        //     ['name' => 'Quotation'],
        //     ['name' => 'Variation Order'],
        //     ['name' => 'Variation Order With Electrical Work'],
        //     ['name' => 'Variation Order For FOC'],
        //     ['name' => 'Variation Order With Cancellation'],
        //     ['name' => 'Project Cover'],
        //     ['name' => 'Purchase Order']
        // ];
        
        // return DB::transaction(function () use ($company,$documentStandardsData) {
        return DB::transaction(function () use ($company) {

            $companyEloquent = CompanyMapper::toEloquent($company);

            $companyEloquent->save();

            if($companyEloquent){
                if (request()->hasFile('qr_code') && request()->file('qr_code')->isValid()) {
                    $companyEloquent->addMediaFromRequest('qr_code')->toMediaCollection('qr_code', 'qr_codes');
                }
            }

            // Create document standards with the company
            // foreach ($documentStandardsData as $documentStandardData) {

            //     $documentStandard = new DocumentStandardEloquentModel($documentStandardData);

            //     $documentStandard->company_id = $companyEloquent->id;

            //     $documentStandard->save();
            // }

            return CompanyData::fromEloquent($companyEloquent);
        });
    }
    public function update(Company $company): Company
    {
        $companyEloquent = CompanyEloquentModel::query()->findOrFail($company->id);

        $companyEloquent->name = $company->name;

        $companyEloquent->tel = $company->tel;

        $companyEloquent->fax = $company->fax;

        $companyEloquent->email = $company->email;

        $companyEloquent->main_office = $company->main_office;

        $companyEloquent->design_branch_studio = $company->design_branch_studio;

        $companyEloquent->hdb_license_no = $company->hdb_license_no;

        $companyEloquent->reg_no = $company->reg_no;

        $companyEloquent->gst_reg_no = $company->gst_reg_no;

        $companyEloquent->gst = $company->gst;

        $companyEloquent->docu_prefix = $company->docu_prefix;

        $companyEloquent->invoice_no_start = $company->invoice_no_start;

        $companyEloquent->fy_start = $company->fy_start;
        
        if($company->customer_invoice_running_number_values){
            $companyEloquent->customer_invoice_running_number_values = $company->customer_invoice_running_number_values;
        }

        if($company->invoice_running_number){
            $companyEloquent->invoice_running_number = $company->invoice_running_number;
        }
        
        if (isset($company->enable_customer_running_number_by_month)) {
            $companyEloquent->enable_customer_running_number_by_month = $company->enable_customer_running_number_by_month;
        }

        $companyEloquent->fy_end = $company->fy_end;
        if($company->quotation_no){
            $companyEloquent->quotation_no = $company->quotation_no;
        }

        if($company->quotation_prefix){
            $companyEloquent->quotation_prefix = $company->quotation_prefix;
        }

        if($company->invoice_prefix){
            $companyEloquent->invoice_prefix = $company->invoice_prefix;
        }

        if($company->logo != null)
        {
            $companyEloquent->logo = $company->logo;
        }
        if($company->company_stamp != null)
        {
            $companyEloquent->company_stamp = $company->company_stamp;
        }

        $companyEloquent->save();
        if($companyEloquent){
            if (request()->hasFile('qr_code') && request()->file('qr_code')->isValid()) {
                $companyEloquent->clearMediaCollection('qr_code');
                $companyEloquent->addMediaFromRequest('qr_code')->toMediaCollection('qr_code', 'qr_codes');
            }
        }
        return $company;
    }

    public function updateDefaultCompany($id)
    {
        $defaultCompnaies = CompanyEloquentModel::where('is_default',true)->update([
            'is_default' => false
        ]);

        $companyEloquent = CompanyEloquentModel::query()->findOrFail($id);

        $companyEloquent->is_default = true;

        $companyEloquent->save();

        return true;


    }

    public function updateAccountingSoftwareCompanyIds(array $data)
    {
        foreach ($data as $credential) {
            QuickBookEloquentModel::updateOrCreate(
                ['company_id' => $credential->company_id],
                [
                    'accounting_software_company_id' => $credential->accounting_software_company_id,
                    'refresh_token' => $credential->refresh_token,
                ]
            );
        }
    }

    public function delete(int $company_id): void
    {
        $companyEloquent = CompanyEloquentModel::query()->findOrFail($company_id);
        $companyEloquent->delete();
    }

    public function increaseQuotationNo(int $company_id)
    {
        $checkCommonQuotationNumSetting = GeneralSettingEloquentModel::where('setting', "enable_common_quotation_running_number")
                                            ->where('value', "true")
                                            ->first();
        if($checkCommonQuotationNumSetting){
            $commonQuotationNum = GeneralSettingEloquentModel::where('setting','common_quotation_start_number')->first();
            $commonQuotationNum->increment('value');
        }else{
            $companyEloquent = CompanyEloquentModel::query()->findOrFail($company_id);
            $companyEloquent->increment('quotation_no');
        }
    }
}