<?php

namespace Src\Company\System\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\System\Domain\Model\Entities\Company;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Illuminate\Support\Facades\Storage;

class CompanyMapper
{
    public static function fromRequest(Request $request, ?int $company_id = null): Company
    {
        if ($request->hasFile('logo')) {

            $logoName =  time().'.'.$request->file('logo')->extension();

            $filePath = 'logo/' . $logoName;
     
            Storage::disk('public')->put($filePath, file_get_contents($request->file('logo')));

            $logo = $logoName;

        } else {
            // Set logo to null if not provided
            $logo = null;
        }

        if ($request->hasFile('company_stamp')) {

            $stampName =  time().'.'.$request->file('company_stamp')->extension();

            $filePath = 'stamp/' . $stampName;
     
            Storage::disk('public')->put($filePath, file_get_contents($request->file('company_stamp')));

            $stamp = $stampName;

        } else {
            // Set logo to null if not provided
            $stamp = null;
        }

        return new Company(
            id: $company_id,
            name: $request->string('name'),
            tel: $request->string('tel'),
            fax: $request->string('fax'),
            email: $request->string('email'),
            main_office: $request->string('main_office'),
            design_branch_studio: $request->string('design_branch_studio'),
            hdb_license_no: $request->string('hdb_license_no'),
            reg_no: $request->string('reg_no'),
            gst_reg_no: $request->string('gst_reg_no'),
            gst: $request->float('gst'),
            logo: $logo,
            company_stamp: $stamp,
            docu_prefix: $request->string('docu_prefix'),
            invoice_no_start: $request->integer('invoice_no_start'),
            fy_start: $request->string('fy_start'),
            fy_end: $request->string('fy_end'),
            quotation_no: $request->integer('quotation_no'),
            customer_invoice_running_number_values: $request->string('customer_invoice_running_number_values'),
            enable_customer_running_number_by_month: $request->boolean('enable_customer_running_number_by_month'),
            quotation_prefix: $request->string('quotation_prefix'),
            invoice_prefix: $request->string('invoice_prefix'),
            invoice_running_number: $request->integer('invoice_running_number')
        );
    }

    public static function fromEloquent(CompanyEloquentModel $companyEloquent): Company
    {
        return new Company(
            id: $companyEloquent->id,
            name: $companyEloquent->name,
            tel: $companyEloquent->tel,
            fax: $companyEloquent->fax,
            email: $companyEloquent->email,
            main_office: $companyEloquent->main_office,
            design_branch_studio: $companyEloquent->design_branch_studio,
            hdb_license_no: $companyEloquent->hdb_license_no,
            reg_no: $companyEloquent->reg_no,
            gst_reg_no: $companyEloquent->gst_reg_no,
            gst: $companyEloquent->gst,
            logo: $companyEloquent->logo,
            company_stamp: $companyEloquent->company_stamp,
            docu_prefix: $companyEloquent->docu_prefix,
            invoice_no_start: $companyEloquent->invoice_no_start,
            fy_start: $companyEloquent->fy_start,
            fy_end: $companyEloquent->fy_end,
            quotation_no: $companyEloquent->quotation_no,
            customer_invoice_running_number_values: $companyEloquent->customer_invoice_running_number_values,
            enable_customer_running_number_by_month: $companyEloquent->enable_customer_running_number_by_month,
            quotation_prefix: $companyEloquent->quotation_prefix,
            invoice_prefix: $companyEloquent->invoice_prefix,
            invoice_running_number: $companyEloquent->invoice_running_number,
        );
    }

    public static function toEloquent(Company $company): CompanyEloquentModel
    {
        $companyEloquent = new CompanyEloquentModel();
        if ($company->id) {
            $companyEloquent = CompanyEloquentModel::query()->findOrFail($company->id);
        }
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
        $companyEloquent->logo = $company->logo;
        $companyEloquent->company_stamp = $company->company_stamp;
        $companyEloquent->docu_prefix = $company->docu_prefix;
        $companyEloquent->invoice_no_start = $company->invoice_no_start;
        $companyEloquent->fy_start = $company->fy_start;
        $companyEloquent->fy_end = $company->fy_end;
        $companyEloquent->quotation_no = $company->quotation_no;
        $companyEloquent->customer_invoice_running_number_values = $company->customer_invoice_running_number_values;
        $companyEloquent->enable_customer_running_number_by_month = $company->enable_customer_running_number_by_month;
        $companyEloquent->quotation_prefix = $company->quotation_prefix;
        $companyEloquent->invoice_prefix = $company->invoice_prefix;
        $companyEloquent->invoice_running_number = $company->invoice_running_number;
        return $companyEloquent;
    }
}