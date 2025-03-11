<?php

namespace Src\Company\System\Application\DTO;

use Illuminate\Http\Request;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;

class CompanyData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $tel,
        public readonly ?string $fax,
        public readonly ?string $email,
        public readonly ?string $main_office,
        public readonly ?string $design_branch_studio,
        public readonly ?string $hdb_license_no,
        public readonly ?string $reg_no,
        public readonly ?string $gst_reg_no,
        public readonly ?float $gst,
        public readonly ?string $logo,
        public readonly ?string $company_stamp,
        public readonly ?string $docu_prefix,
        public readonly ?int $invoice_no_start,
        public readonly ?string $fy_start,
        public readonly ?string $fy_end,
        public readonly ?int $quotation_no,
        public readonly ?string $customer_invoice_running_number_values,
        public readonly ?bool $enable_customer_running_number_by_month,
        public readonly ?string $quotation_prefix,
        public readonly ?string $invoice_prefix,
        public readonly ?int $invoice_running_number
    )
    {}

    public static function fromRequest(Request $request, ?int $company_id = null): CompanyData
    {
        return new self(
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
            logo: $request->string('logo'),
            company_stamp: $request->string('company_stamp'),
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

    public static function fromEloquent(CompanyEloquentModel $companyEloquent): self
    {
        return new self(
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
            invoice_running_number: $companyEloquent->invoice_running_number
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tel' => $this->tel,
            'fax' => $this->fax,
            'email' => $this->email,
            'main_office' => $this->main_office,
            'design_branch_studio' => $this->design_branch_studio,
            'hdb_license_no' => $this->hdb_license_no,
            'reg_no' => $this->reg_no,
            'gst_reg_no' => $this->gst_reg_no,
            'gst' => $this->gst,
            'logo' => $this->logo,
            'company_stamp' => $this->company_stamp,
            'docu_prefix' => $this->docu_prefix,
            'invoice_no_start' => $this->invoice_no_start,
            'fy_start' => $this->fy_start,
            'fy_end' => $this->fy_end,
            'quotation_no' => $this->quotation_no,
            'customer_invoice_running_number_values' => $this->customer_invoice_running_number_values,
            'enable_customer_running_number_by_month' => $this->enable_customer_running_number_by_month,
            'quotation_prefix' => $this->quotation_prefix,
            'invoice_prefix' => $this->invoice_prefix,
            'invoice_running_number' => $this->invoice_running_number
        ];
    }
}